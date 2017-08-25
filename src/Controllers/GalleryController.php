<?php

namespace Stylers\Media\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Stylers\Media\Models\Gallery;
use Stylers\Media\Entities\GalleryEntity;
use Stylers\Media\Models\GalleryItem;
use Stylers\Taxonomy\Models\Taxonomy;
use Stylers\Taxonomy\Manipulators\DescriptionSetter;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request) {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        return [
            'success' => true,
            'data' => (new GalleryEntity(Gallery::findOrFail($id)))->getFrontendData(),
            'options' => GalleryEntity::getOptions()
        ];
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id) {
        $gallery = Gallery::findOrFail($id);
        $gallery->fill($request->toArray());
        $gallery->name_description_id = $request->name ? (new DescriptionSetter($request->name, $gallery->name_description_id))->set()->id : null;
        $gallery->role_taxonomy_id = $request->role ? Taxonomy::getTaxonomy($request->role, Config::get('media.gallery_role'))->id : null;
        $gallery->saveOrFail();

        return [
            'success' => true,
            'data' => (new GalleryEntity($gallery))->getFrontendData(),
            'options' => GalleryEntity::getOptions()
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        GalleryItem::where('gallery_id', $id)->delete();
        $gallery = Gallery::findOrFail($id);
        return [
            'success' => (bool) $gallery->delete(),
            'data' => (new GalleryEntity(Gallery::withTrashed()->findOrFail($id)))->getFrontendData(),
            'options' => GalleryEntity::getOptions()
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function updatePriority(Request $request, $id) {
        foreach ($request->input('items') as $item) {
            $galleryItem = GalleryItem::findOrFail($item['id']);
            $galleryItem->priority = $item['priority'];
            $galleryItem->saveOrFail();
        }
        return [
            'success' => true,
            'data' => (new GalleryEntity(Gallery::findOrFail($id)))->getFrontendData(),
            'options' => GalleryEntity::getOptions()
        ];
    }
}