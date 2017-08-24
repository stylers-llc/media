<?php

namespace Stylers\Media\Entities;

use Illuminate\Support\Facades\Config;
use Stylers\Media\Models\Gallery;
use Stylers\Taxonomy\Entities\DescriptionEntity;
use Stylers\Taxonomy\Models\Taxonomy;
use Stylers\Taxonomy\Entities\TaxonomyEntity;

class GalleryEntity
{
    protected $gallery;

    public function __construct(Gallery $gallery) {

        $this->gallery = $gallery;
    }

    public function getFrontendData(array $additions = []) {
        $return = [
            'id' => $this->gallery->id,
            'name' => $this->gallery->name_description_id ? (new DescriptionEntity($this->gallery->name))->getFrontendData() : null,
            'galleryable_id' => $this->gallery->galleryable_id,
            'galleryable_type' => $this->gallery->galleryable_type,
            'role' => $this->gallery->role_taxonomy_id ? $this->gallery->role->name : null,
            'items' => $this->getItems()
        ];

        return $return;
    }

    protected function getItems() {
        $files = [];
        foreach ($this->gallery->items as $item) {
            $files[] = (new FileEntity($item->file))->getFrontendData(['gallery_item']);
        }
        return $files;
    }

    static public function getOptions() {
        $roleEn = new TaxonomyEntity(Taxonomy::findOrFail(Config::get('taxonomies.gallery_role')));
        $typeEn = new TaxonomyEntity(Taxonomy::findOrFail(Config::get('taxonomies.file_type')));

        return [
            'role' => $roleEn->getFrontendData(['descendants', 'translations']),
            'type' => $typeEn->getFrontendData(['descendants', 'translations'])
        ];
    }

    static public function getCollection($models, array $additions = []) : array
    {
        $return = [];
        foreach ($models as $model) {
            $return[] = (new static($model))->getFrontendData($additions);
        }
        return $return;
    }
}