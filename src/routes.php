<?php

use Illuminate\Support\Facades\Artisan;
use Shridhar\Bower\Bower;

Artisan::command("bower:install {name} {--path=}", function($name, $path = null) {
    Bower::make()->getComponent($name)->install();
});

Artisan::command("bower:uninstall {name} {--path=}", function($name, $path = null) {
    Bower::make()->getComponent($name)->uninstall();
});
