<?php

namespace App\Leonardo\Facades;

class PathInfo {

    /** @var array */
    private $pathinfo = [
        'dirname'       => null,
        'basename'      => null,
        'extension'     => null,
        'filename'      => null,
    ];

    /** @var string */
    private $dirname;

    /** @var string */
    private $basename;

    /** @var string */
    private $extension;

    /** @var string */
    private $filename;

    /** @var string */
    private $path;

    public function __construct($filename) {
        $this->pathinfo = pathinfo($filename);

        $this->dirname      = $this->pathinfo['dirname'];
        $this->basename     = $this->pathinfo['basename'];
        $this->extension    = $this->pathinfo['extension'];
        $this->filename     = $this->pathinfo['filename'];
        $this->path         = $this->dirname . '/' . $this->basename;
    }

    /**
     * @return string
     */
    public function dirname() {
        return $this->dirname;
    }

    /**
     * @return string
     */
    public function basename() {
        return $this->basename;
    }

    /**
     * @return string
     */
    public function extension() {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function filename() {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function path() {
        return $this->path;
    }


    /**
     * @return bool
     */
    public function delete() {

        if(file_exists($this->path) && !is_dir($this->path)) {
            return unlink($this->path);
        }

        return false;
    }

}
