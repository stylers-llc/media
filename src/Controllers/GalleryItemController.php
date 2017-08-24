<?php
/**
 * Created by PhpStorm.
 * User: sty021
 * Date: 2017.08.24.
 * Time: 12:03
 */

namespace Stylers\Media\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Stylersmedia\Entities\File;
use Modules\Stylersmedia\Entities\FileEntity;
use Modules\Stylersmedia\Entities\GalleryItem;
use Modules\Stylersmedia\Manipulators\FileSetter;

class GalleryItemController extends Controller {

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request) {
        $file = (new FileSetter($request->all()))->set();
        $galleryItem = new GalleryItem();
        $galleryItem->file_id = $file->id;
        $galleryItem->gallery_id = $request->input('gallery_id');
        $galleryItem->is_highlighted = $request->input('highlighted');
        $galleryItem->saveOrFail();
        return ['success' => true, 'data' => (new FileEntity($file))->getFrontendData(['gallery_item'])];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        return ['success' => true, 'data' => (new FileEntity(File::findOrFail($id)))->getFrontendData(['gallery_item'])];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id) {
        $attributes = $request->all();
        $attributes['id'] = $id;
        if ($request->exists('highlighted')) {
            $galleryItem = GalleryItem::where('file_id', $id)->where('gallery_id', $request->input('gallery_id'))->firstOrFail();
            $galleryItem->is_highlighted = $request->input('highlighted');
            $galleryItem->saveOrFail();
        }
        $file = (new FileSetter($attributes))->set();
        return ['success' => true, 'data' => (new FileEntity($file))->getFrontendData(['gallery_item'])];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        GalleryItem::where('file_id', $id)->delete();
        $file = File::findOrFail($id);
        return [
            'success' => (bool) $file->delete(),
            'data' => (new FileEntity(File::withTrashed()->findOrFail($id)))->getFrontendData(['gallery_item'])
        ];
    }

    public function upload(Request $request) {
        $file = (new FileSetter($request->toArray()))->setBySymfonyFile($request->file('file'));

        $galleryItem = new GalleryItem();
        $galleryItem->file_id = $file->id;
        $galleryItem->gallery_id = $request->input('gallery_id');
        $galleryItem->saveOrFail();

        return ['success' => true, 'data' => (new FileEntity($file))->getFrontendData(['gallery_item'])];
    }

}