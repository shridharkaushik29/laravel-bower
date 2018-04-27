<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Shridhar\Bower;

use Illuminate\Filesystem\Filesystem;
use Collective\Html\HtmlFacade;
use SplFileInfo;

/**
 * Description of Asset
 *
 * @author Shridhar
 */
class Asset extends SplFileInfo {

    protected $app, $filesystem, $version, $params, $url, $type;

    public function __construct(Filesystem $filesystem, $path = null, $url = null, $type = null, $version = null, $params = []) {
        $this->version = $version;
        $this->path = $path;
        $this->type = $type;
        $this->url = $url;
        $this->params = $params;
        $this->filesystem = $filesystem;
        parent::__construct($this->path());
    }

    public static function make($config) {
        return app()->makeWith(__CLASS__, $config);
    }

    public function url() {
        return $this->url;
    }

    public function path() {
        return public_path($this->path);
    }

    public function is_local() {
        if (!empty($this->path)) {
            return true;
        } else {
            return false;
        }
    }

    public function exists() {
        $path = $this->path();
        return file_exists($path);
    }

    public function mime() {
        $mime = $this->filesystem->mimeType($this->path());
        return $mime;
    }

    protected function minified($path) {
        return preg_replace("/(\.)([^\.]+)$/", ".min.$2", $path);
    }

    public function type() {
        if (!empty($this->type)) {
            $type = $this->type;
        } else {
            $type = $this->getExtension();
        }
        return $type;
    }

    public function tag($config = []) {
        $params = collect(array_get($config, "params") ?: $this->params ?: []);
        $version = array_get($config, "version") ?: $this->version;
        $url = $this->url();

        if (!empty($version)) {
            $params->put("version", $version);
        }

        if ($params->count()) {
            $url .= "?" . $params->map(function($value, $key) {
                        return "$key=$value";
                    })->implode("&");
        }

        switch ($this->type()) {
            case 'js':
                $tag = "<script src=\"$url\"></script>";
                break;

            case 'css':
                $tag = "<link href=\"$url\" rel=\"stylesheet\" />";
                break;

            case 'favicon':
                $tag = HtmlFacade::favicon($url);
                break;
        }

        return @$tag;
    }

    static function googleFont($name, $params = []) {
        $params["family"] = $name;
        $url = "https://fonts.googleapis.com/css";
        return static::make([
                    "url" => $url,
                    "type" => "css",
                    "params" => $params
        ]);
    }

}
