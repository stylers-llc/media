<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Stylers\Taxonomy\Models\Taxonomy;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->seedFileTypes();
        $this->seedGalleryRoles();
    }

    protected function seedFileTypes() {
        $parentTx = Taxonomy::loadTaxonomy(Config::get('media.file_type'));
        $parentTx->name = 'file_type';
        $parentTx->save();

        foreach (Config::get('media.file_types') as $name => $id) {
            $tx = Taxonomy::loadTaxonomy($id);
            $tx->name = $name;
            $tx->save();
            $tx->makeChildOf($parentTx);
        }
    }

    protected function seedGalleryRoles() {
        $parentTx = Taxonomy::loadTaxonomy(Config::get('media.gallery_role'));
        $parentTx->name = 'gallery_role';
        $parentTx->save();

        foreach (Config::get('media.gallery_roles') as $name => $id) {
            $tx = Taxonomy::loadTaxonomy($id);
            $tx->name = $name;
            $tx->save();
            $tx->makeChildOf($parentTx);
        }
    }
}