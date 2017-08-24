<?php

namespace Stylers\Media\Manipulators;

use App\Exceptions\UserException;
use Illuminate\Support\Facades\Config;
use Stylers\Media\Models\Gallery;
use Stylers\Taxonomy\Entities\Taxonomy;
use Stylers\Taxonomy\Manipulators\DescriptionSetter;

class GallerySetter
{
    private $connection;
    private $attributes = [
        'id' => null,
        'galleryable_id' => null,
        'galleryable_type' => null,
        'name_description_id' => null,
        'role_taxonomy_id' => null,
        'priority' => null
    ];

    public function __construct(array $attributes) {
        foreach ($attributes as $key => $value) {
            if (array_key_exists($key, $this->attributes)) {
                $this->attributes[$key] = $value;
            }
        }
    }

    /**
     * Set the connection associated with the model.
     * @param string $name
     * @return $this
     */
    public function setConnection($name) {
        $this->connection = $name;
        return $this;
    }

    /**
     * Creates new gallery and throws error in case of any overlap
     * @return Gallery
     * @throws UserException
     */
    public function set() {
        if (isset($this->attributes['priority']) && $this->priorityExists($this->attributes['galleryable_id'], $this->attributes['galleryable_type'], $this->attributes['priority'])) {
            throw new UserException('Priority already exists.');
        }

        if ($this->attributes['id']) {
            $gallery = Gallery::on($this->connection)->findOrFail($this->attributes['id']);
        } else {
            $gallery = new Gallery();
        }
        $gallery->fill($this->attributes);

        if (!empty($this->attributes['description'])) {
            $description = (new DescriptionSetter($this->attributes['description']))->setConnection($this->database)->set();
            $gallery->name_description_id = $description->id;
        }

        if (!empty($this->attributes['role'])) {
            $gallery->role_taxonomy_id = Taxonomy::getTaxonomy($this->attributes['role'], Config::get('taxonomies.gallery_role'), $this->connection)->id;
        }

        $gallery->setConnection($this->connection);
        $gallery->saveOrFail();

        return $gallery;
    }

    private function priorityExists($galleryableId, $galleryableType, $priority) {
        $count = Gallery::on($this->connection)
            ->where('galleryable_id', '=', $galleryableId)
            ->where('galleryable_type', '=', $galleryableType)
            ->where('priority', '=', $priority)
            ->count();
        return ($count > 0);
    }
}