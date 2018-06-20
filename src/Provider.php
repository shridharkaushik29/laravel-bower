<?php

namespace Shridhar\Bower;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider {

    protected $files = [];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {

        $this->loadRoutesFrom(__DIR__ . "/routes.php");

        Bower::$components_info = array_wrap(config("bower.components_info"));
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->publishes([
            __DIR__ . '/config/bower.php' => config_path('bower.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $path = base_path("bower_components");
            $link = public_path("bower_components");
            if (!file_exists($path)) {
                mkdir($path);
            }
            @symlink($path, $link);
        }
    }

}
