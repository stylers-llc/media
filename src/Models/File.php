<?php
namespace Stylers\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Stylers\Taxonomy\Models\Description;
use Stylers\Taxonomy\Models\Taxonomy;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class File extends Model {

    use SoftDeletes;

    protected $fillable = [
        'extension', 'path', 'type_taxonomy_id', 'description_id', 'width', 'height'
    ];

    public function type() {
        return $this->hasOne(Taxonomy::class, 'id', 'type_taxonomy_id');
    }

    public function description() {
        return $this->hasOne(Description::class, 'id', 'description_id');
    }

    public function galleryItem() {
        return $this->hasOne(GalleryItem::class, 'file_id', 'id');
    }

    public function getAbsolutePath() {
        return $this->getUploadDirectory(public_path(), Config::get('media.media_image_dir')) . '/' . $this->path;
    }

    public function getExtension(SymfonyFile $symfonyFile = null) {
        return $symfonyFile ? $symfonyFile->guessExtension() : $this->extension;
    }

    public function getPath(SymfonyFile $symfonyFile = null, $thumbName = null, $absolute = false) {
        $root = $this->getUploadDirectory(public_path(), Config::get('media.media_image_dir')) . '/';
        $ext = $this->getExtension($symfonyFile);

        $path = sprintf('%08s', floor($this->id / 1000000) * 1000000) . '/';
        if (!is_dir($root . $path)) {
            mkdir($root . $path, 0777);
        }

        $path .= sprintf('%08s', floor($this->id / 1000) * 1000) . '/';
        if (!is_dir($root . $path)) {
            mkdir($root . $path, 0777);
        }

        $path .= sprintf('%08s', $this->id);
        if (!empty($thumbName)) {
            $path .= '_' . $thumbName;
        }
        if (!empty($ext)) {
            $path .= '.' . $ext;
        }

        return ($absolute ? $root : '') . $path;
    }

    private function getUploadDirectory($root, $path) {
        if (!is_dir($root)) {
            mkdir($root, 0777);
        }
        $trimmedRoot = rtrim($root, '/');
        $pathParts = explode('/', trim($path, '/'));
        foreach ($pathParts as $pathPart) {
            $trimmedRoot .= '/' . $pathPart;
            if (!is_dir($trimmedRoot)) {
                mkdir($trimmedRoot, 0777);
            }
        }
        return $trimmedRoot;
    }

    public function getImageInfo() {
        $path = $this->getAbsolutePath();
        if (!file_exists($path)) {
            return false;
        }
        $info = getimagesize($path);
        if (!$info) {
            return false;
        }
        $return = ['width' => $info[0], 'height' => $info[1], 'type' => $info[2], 'extension' => null, 'mime' => $info['mime']];
        switch ($return['type']) {
            case IMAGETYPE_GIF: $return['extension'] = 'gif'; break;
            case IMAGETYPE_JPEG: $return['extension'] = 'jpg'; break;
            case IMAGETYPE_PNG: $return['extension'] = 'png'; break;
        }
        return $return;
    }

    public function isSupportedImage() {
        $info = $this->getImageInfo();
        return $info && $info['extension'];
    }

    static public function getFromGalleries(array $galleryIds, $database = null) {
        if (empty($galleryIds)) {
            return [];
        }
        $result = DB::connection($database)
            ->table('files')
            ->select('files.*')
            ->join('gallery_items', 'files.id', '=', 'gallery_items.file_id')
            ->whereIn('gallery_id', $galleryIds)
            ->orderBy('priority')
            ->get();
        return self::hydrate($result);
    }
}