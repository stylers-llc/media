<?php

namespace Stylers\Media\Entities;

use Illuminate\Support\Facades\Config;
use Stylers\Taxonomy\Entities\DescriptionEntity;

class FileEntity {

    protected $file;

    public function __construct(File $file) {
        $this->file = $file;
    }

    public function getFrontendData(array $additions = []) {
        $return = [
            'id' => $this->file->id,
            'extension' => $this->file->extension,
            'path' => self::getRoot() . $this->file->path,
            'thumbnails' => $this->getThumbnails(),
            'width' => $this->file->width,
            'height' => $this->file->height,
            'type' => $this->file->type_taxonomy_id ? $this->file->type->name : null,
            'description' => $this->file->description_id ? (new DescriptionEntity($this->file->description))->getFrontendData() : null
        ];

        if (in_array('gallery_item', $additions)) {
            $item = $this->file->galleryItem;
            if ($item) {
                $return['priority'] = $item->priority;
                $return['highlighted'] = $item->is_highlighted;
                $return['gallery_id'] = $item->gallery_id;
            }
        }
        return $return;
    }

    static public function getRoot() {
        return 'storage/' . Config::get('media.media_image_dir') . '/';
    }

    private function getThumbnails() {
        $return = [];
        $breakpoints = Config::get('media.media_width_breakpoints');

        foreach ($breakpoints as $name => $width) {
            $return[] = ['path' => $this->getRoot() . $this->file->getPath(null, $name), 'width' => $width];
        }

        return $return;
    }

}