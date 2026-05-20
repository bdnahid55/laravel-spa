<?php

namespace Sakib\LaravelSpa;

use Illuminate\Support\ServiceProvider;

class SpaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../resources/spa-engine.js' => public_path('vendor/laravel-spa/spa-engine.js'),
        ], 'spa-assets');
    }

    public function register() {}
}