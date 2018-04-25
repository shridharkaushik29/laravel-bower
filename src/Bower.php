<?php

namespace Shridhar\Bower;

/**
 * Description of Bower
 *
 * @author Shridhar
 */
class Bower {

    protected static $__config = [];
    public static $components_info;
    protected $components;

    public function __construct($components = []) {
        $this->components = $components;
    }

    static function make($config) {
        return app()->makeWith(__CLASS__, $config);
    }

    function components($names = []) {
        $components = collect($names ?: $this->components)->map(function($name) {
            return $this->getComponent($name);
        });
        return $components;
    }

    static function getComponent($name) {
        return Component::make($name);
    }

    function tags($names = []) {
        $components = $this->components($names);
        $tags = $components->map(function($component) {
                    return $component->allFiles();
                })->flatten()->unique()->map(function($file) {
            return $file->tag();
        });
        return $tags;
    }

    function tags_string() {
        $tags = $this->tags();
        $string = implode("", $tags->toArray());
        return $string;
    }

}

Bower::$components_info = [
    "bootstrap" => [
        "main" => [
            "dist/css/bootstrap.min.css",
            "dist/js/bootstrap.min.js"
        ]
    ],
    "font-awesome" => [
        "main" => [
            "web-fonts-with-css/css/fontawesome-all.min.css"
        ]
    ],
    "lodash" => [
        "main" => [
            "dist/lodash.min.js"
        ]
    ]
];
