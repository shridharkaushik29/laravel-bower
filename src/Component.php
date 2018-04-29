<?php

namespace Shridhar\Bower;

use Illuminate\Support\Facades\File;

/**
 * Description of Component
 *
 * @author Shridhar
 */
class Component {

    protected $config, $__name;
    protected $base_path, $base_url;

    protected function getConfig($key) {
        return array_get($this->config, $key);
    }

    public function __construct($name, $base_path = null, $base_url = null) {
        $this->__name = $name;
        $this->base_path = $base_path;
        $this->base_url = $base_url;
    }

    public static function make($config) {
        return app()->makeWith(__CLASS__, $config);
    }

    public function name() {
        return $this->__name;
    }

    function install() {
        $dir = dirname($this->base_path);
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        chdir($dir);
        Bower::run_command("install $this->__name");
    }

    function installed() {
        return file_exists("$this->base_path/$this->__name/.bower.json");
    }

    function copy($destinaation) {
        File::copyDirectory("$this->base_path/$this->__name", $destinaation);
    }

    function uninstall() {
        $dir = dirname($this->base_path);
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        chdir($dir);
        Bower::run_command("uninstall $this->__name");
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
        return $this->base_path . "/$name/$path";
    }

    public function url($path) {
        $name = $this->name();
        return $this->base_url . "/$name/$path";
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
                $dep = static::make([
                            "name" => $dep,
                            "base_path" => $this->base_path,
                            "base_url" => $this->base_url,
                ]);
            }
        } else {
            $deps = [];
        }
        return collect($deps);
    }

}
