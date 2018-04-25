<?php

namespace Shridhar\Bower;

/**
 * Description of Component
 *
 * @author Shridhar
 */
class Component {

    protected $config, $__name;
    static $base_path, $base_url;

    protected function getConfig($key) {
        return array_get($this->config, $key);
    }

    public function __construct($name) {
        $this->__name = $name;
    }

    public static function make($name) {
        return app()->makeWith(__CLASS__, [
                    "name" => $name
        ]);
    }

    public function name() {
        return $this->__name;
    }

    public function asset($path, $meta = []) {
        return Asset::make([
                    "path" => $this->path($path),
                    "url" => $this->url($path),
                    "version" => array_get($meta, "version"),
                    "meta" => $meta
        ]);
    }

    public function path($path) {
        $name = $this->name();
        return static::$base_path . "/$name/$path";
    }

    public function url($path) {
        $name = $this->name();
        return static::$base_url . "/$name/$path";
    }

    public function info() {
        $path = $this->path(".bower.json");
        $default_info = array_get(Bower::$components_info, $this->name());
        if (file_exists($path)) {
            $contents = file_get_contents($path);
            if ($default_info) {
                $array = collect($default_info)->merge(json_decode($contents));
            } else {
                $array = json_decode($contents);
            }
            return collect($array);
        } else {
            return collect();
        }
    }

    public function files() {
        $info = $this->info();
        $main_files = collect($info->get("main"))->map(function(&$file) use($info) {
            return $this->asset($file, [
                        "version" => $info->get("_release"),
                        "relative_path" => $file,
            ]);
        });
        return $main_files;
    }

    public function allFiles() {
        $deps = $this->dependencies();
        $files = $deps->map(function($dep) {
                    return $dep->allFiles();
                })->merge($this->files())->flatten();
        return $files;
    }

    public function dependencies() {
        $info = $this->info();
        if ($info) {
            $deps_array = $info->get("dependencies");
            $deps = array_keys((array) $deps_array);
            foreach ($deps as &$dep) {
                $dep = app()->makeWith(__CLASS__, [
                    "name" => $dep
                ]);
            }
        } else {
            $deps = [];
        }
        return collect($deps);
    }

}

Component::$base_path = public_path("assets/bower_components");
Component::$base_url = url("assets/bower_components");
