###Install component
1. add Stylers\Media\Providers\MediaServiceProvider::class to config/app providers
2. php artisan vendor:publish --provider="Stylers\Media\Providers\MediaServiceProvider"
3. php artisan db:seed --class=MediaSeeder