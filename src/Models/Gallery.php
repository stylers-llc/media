<?php
/**
 * Created by PhpStorm.
 * User: sty021
 * Date: 2017.08.24.
 * Time: 11:49
 */

namespace Stylers\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stylers\Taxonomy\Models\Description;
use Stylers\Taxonomy\Models\Taxonomy;

class Gallery extends Model {

    use SoftDeletes;

    protected $fillable = [
        'galleryable_id', 'galleryable_type', 'name_description_id', 'role_taxonomy_id', 'priority'
    ];

    public function name() {
        return $this->hasOne(Description::class, 'id', 'name_description_id');
    }

    public function role() {
        return $this->hasOne(Taxonomy::class, 'id', 'role_taxonomy_id');
    }

    public function items() {
        return $this->hasMany(GalleryItem::class, 'gallery_id', 'id')->orderBy('gallery_items.priority');
    }

    public function galleryable() {
        return $this->morphTo();
    }

}