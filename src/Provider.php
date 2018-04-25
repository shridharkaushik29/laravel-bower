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
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        
    }

}
