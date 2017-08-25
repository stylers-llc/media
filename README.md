###Install component
1. add Stylers\Media\Providers\MediaServiceProvider::class to config/app providers
2. php artisan vendor:publish --provider="Stylers\Media\Providers\MediaServiceProvider"
3. php artisan db:seed --class=MediaSeeder
4. create folder what you set in config/media.php media_image_dir and add 777 permission for it