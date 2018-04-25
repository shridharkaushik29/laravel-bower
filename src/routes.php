<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command("bower:install {name}", function($name) {
    $assets_path = public_path("assets");
    @mkdir($assets_path);
    chdir($assets_path);
    system("bower install $name");
});

Artisan::command("bower:uninstall {name}", function($name) {
    $assets_path = public_path("assets");
    @mkdir($assets_path);
    chdir($assets_path);
    system("bower uninstall $name");
});
