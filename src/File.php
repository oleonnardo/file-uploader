<?php

namespace App\Leonardo;

class File {

    /** @var array */
    private $files = [
        'name'      => null,
        'type'      => null,
        'tmp_name'  => null,
        'error'     => null,
        'size'      => null,
    ];

    /** @var string */
    private $paramName;

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var string */
    private $tmp_name;

    /** @var int */
    private $error;

    /** @var int */
    private $size;

    /**
     * File constructor.
     * @param array $attributes
     */
    public function __construct($param = '', $index = null) {

        // inicialização da classe
        $this->initClass($param, $index);

    }


    /**
     * @return array
     */
    public function getInstanceMultiple() {
        $newFile = [];

        for($index = 0; $index < count($this->getOriginalName()); $index++) {
            $newFile[] = new File($this->paramName, $index);
        }

        return $newFile;
    }


    /**
     * @param $param
     * @return array|mixed
     */
    private function initClass($param, $index = null) {

        if(isset($_FILES[$param])) {
            $this->files = $_FILES[$param];
        }

        if(is_numeric($index)) {

            $this->name         = $this->files['name'][$index];
            $this->type         = $this->files['type'][$index];
            $this->tmp_name     = $this->files['tmp_name'][$index];
            $this->error        = $this->files['error'][$index];
            $this->size         = $this->files['size'][$index];
            $this->paramName    = $param;

        } else {

            $this->name         = $this->files['name'];
            $this->type         = $this->files['type'];
            $this->tmp_name     = $this->files['tmp_name'];
            $this->error        = $this->files['error'];
            $this->size         = $this->files['size'];
            $this->paramName    = $param;

        }

        return $this->files;
    }


    /**
     * @param $destiny
     * @param $filename
     * @return bool
     */
    public function move($destiny, $filename) {

        if($this->exists($destiny) === false) {
            $this->makeDirectory($destiny);
        }

        return move_uploaded_file($this->tmp_name, $destiny.$filename);
    }

    /**
     * @param $param
     * @return File
     */
    public function get($param) {
        return new File($param);
    }


    /**
     * @return array
     */
    public function getFile() {
        return $this->files;
    }


    /**
     * @return string
     */
    public function getOriginalName() {
        return $this->name;
    }


    /**
     * @return int
     */
    public function getError() {
        return $this->error;
    }


    /**
     * @return int
     */
    public function getSize() {
        return $this->size;
    }


    /**
     * @return string
     */
    public function getTempName() {
        return $this->tmp_name;
    }


    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }


    /**
     * @param $directory
     * @return bool
     */
    private function exists($file) {
        return file_exists($file);
    }


    /**
     * @return string
     */
    public function getOriginalExtension() {
        $extensao = str_replace('.', '', strrchr($this->getOriginalName(), '.'));

        return $extensao ? $extensao : '';
    }


    /**
     * @return bool
     */
    public function hasMultipleFiles() {
        return is_array($this->name);
    }


    /**
     * @param      $pathname
     * @param int  $mode
     * @param bool $recursive
     * @param null $context
     * @return bool
     */
    private function makeDirectory($pathname, $mode = 0777, $recursive = false, $context = null) {
        return mkdir($pathname, $mode, $recursive, $context);
    }

}
