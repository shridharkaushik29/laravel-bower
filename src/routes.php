<?php

use Illuminate\Support\Facades\Artisan;
use Shridhar\Bower\Bower;
use Illuminate\Filesystem\Filesystem;

Artisan::command("bower:install {name} {--path=}", function($name, $path = null) {
    Bower::make()->getComponent($name)->install();
});

Artisan::command("bower:link", function($name, $path = null) {
    $path = base_path("bower_components");
    $link = public_path("bower_components");
    if (!file_exists($path)) {
        mkdir($path);
    }
    $fs = new Filesystem();
    $fs->link($path, $link);
    $this->info("bower_components directory linked to public directory");
});

Artisan::command("bower:uninstall {name} {--path=}", function($name, $path = null) {
    Bower::make()->getComponent($name)->uninstall();
});
