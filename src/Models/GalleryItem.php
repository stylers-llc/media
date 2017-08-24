<?php

namespace Stylers\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GalleryItem extends Model {

    use SoftDeletes;

    protected $fillable = [
        'gallery_id', 'file_id', 'priority', 'is_highlighted'
    ];

    public function file() {
        return $this->hasOne(File::class, 'id', 'file_id');
    }

    public function gallery() {
        return $this->hasOne(Gallery::class, 'id', 'gallery_id');
    }

}