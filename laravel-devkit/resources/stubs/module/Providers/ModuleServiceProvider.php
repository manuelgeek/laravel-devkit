<?php

namespace DummyNamespace\Providers;

use LaravelDevkit\Support\ServiceProvider;

class DummyProvider extends ServiceProvider
{
    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(module_path('ResourcesViewsMapping','DummyBase'), 'DummySlug');
        $this->loadMigrationsFrom(module_path('DatabaseMigrationsMapping','DummyBase'), 'DummySlug');
        $this->loadConfigsFrom(module_path('ConfigMapping','DummyBase'));
        $this->loadFactoriesFrom(module_path('DatabaseFactoriesMapping','DummyBase'));
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
