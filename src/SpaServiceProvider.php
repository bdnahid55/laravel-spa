<?php

namespace Sakib\LaravelSpa;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class SpaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::directive('spa', function () {
            return 'data-spa';
        });

        Blade::directive('spaContent', function () {
            return 'data-spa-content';
        });

        Blade::directive('spaEngine', function () {
            $url = asset('vendor/laravel-spa/spa-engine.js');
            return "<?php echo '<script src=\"{$url}\" defer></script>'; ?>";
        });

        $this->publishes([
            __DIR__ . '/../resources/spa-engine.js' => public_path('vendor/laravel-spa/spa-engine.js'),
        ], 'spa-assets');
    }

    public function register() {}
}