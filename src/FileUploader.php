<?php

namespace App\Leonardo;

use App\Leonardo\File;

class FileUploader {

    /** @var $_FILES */
    private $files;

    /** @var bool */
    private $multiple = false;

    /** @var string */
    private $path = './';

    /** @var string  */
    private $paramName = 'file';

    /** @var string|null */
    private $filename;

    /** @var bool */
    private $generateFilename = true;


    /**
     * @param string $param
     * @return array|string|null
     * @throws FileException
     */
    public function save($param = '') {

        // define nome do parâmetro
        $this->parameter($param);

        $filename;
        $diretorio  = $this->getPath();
        $file       = new File($this->paramName);

        if ($this->multiple || $file->hasMultipleFiles()) {

            foreach($file->getInstanceMultiple() as $item) {
                $filename[] = $this->processamento($item);
            }

        } else {

            $filename = $this->processamento($file);

        }

        return $filename;
    }


    /**
     * @param \App\Leonardo\File $file
     * @return string|null
     * @throws FileException
     */
    public function processamento(File $file) {
        $filename = null;

        if($file->getError() === 0) {
            $file->move(
                $this->getPath(),
                $filename = $this->getFilename(
                    $file->getOriginalName(),
                    $file->getOriginalExtension()
                )
            );
        } else {

            throw new FileException("Erro no upload do arquivo: " . $file->getError());

        }

        return $filename;
    }


    /**
     * @param boolean $on
     * @return $this
     */
    public function multipleFiles($on = true) {
        $this->multiple = $on;

        return $this;
    }


    /**
     * @param string $param
     * @return $this
     */
    public function parameter($param) {
        $this->paramName = empty($param) ? $this->paramName : $param;

        return $this;
    }


    /**
     * @param string $relativePath
     * @return $this
     */
    public function path($relativePath) {
        $this->path = $relativePath;

        return $this;
    }


    /**
     * @return string
     * @throws FileException
     */
    private function getPath() {

        if(empty($this->path)) {
            throw new FileException('O diretório não foi especificado. Experimentar usar a função path(string)');
        }

        return $this->path;
    }


    /**
     * @param string $filename
     * @return $this
     */
    public function filename($filename) {
        $this->filename = $filename;

        return $this;
    }


    /**
     * @param $originalName
     * @return string|null
     */
    private function getFilename($originalName, $extension) {

        if(empty($this->filename)) {
            if($this->generateFilename) {
                return uniqid(date('dmYHis')).".".$extension;
            }

            return $originalName;
        }

        return $this->filename . "." . $extension;
    }

}
