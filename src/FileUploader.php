<?php

namespace App\Leonardo;

use App\Leonardo\Facades\File;
use App\Leonardo\Facades\FileValidation;
use App\Leonardo\Facades\PathInfo;
use App\Leonardo\FileException;

class FileUploader {

    use FileValidation;

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
     * @param string $filename
     * @return PathInfo|void
     */
    public function get($filename) {

        if($this->exists($filename)) {
            return new PathInfo($filename);
        }

        return;
    }


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

        $files = $this->files;

        if (empty($files)) {
            $files = new File($this->paramName);
        }

        if ($this->multiple || $files->hasMultipleFiles()) {

            foreach($files->getInstanceMultiple() as $item) {
                $filename[] = $this->processamento($item);
            }

        } else {

            $filename = $this->processamento($files);

        }

        return $filename;
    }


    /**
     * @param null $oldFilename
     * @param null $param
     * @return null|string
     * @throws \App\Leonardo\FileException
     */
    public function update($oldFilename = null, $param = null) {

        // define nome do parâmetro
        $this->parameter($param);

        $filename = (strrchr($oldFilename, '/')) ?
            str_replace('/', '', strrchr($oldFilename, '/')) :
            $oldFilename;
        $diretorio  = $this->getPath();

        $file = empty($this->files) ? new File($this->paramName) : $this->files;

        if($file->hasMultipleFiles() === false) {

            $this->processamento($file);

            if ($this->exists($diretorio . $filename)) {
                $this->get($diretorio.$filename)->delete();
            }
        }

        return $filename;
    }


    /**
     * @param \App\Leonardo\File $file
     * @return string|null
     * @throws FileException
     */
    private function processamento(File $file) {
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
            throw new \App\Leonardo\FileException("Erro no upload do arquivo: " . $file->getError());
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


    /**
     * @param $filename
     * @return bool
     */
    private function exists($filename) {
        return file_exists($filename) && !is_dir($filename);
    }
}
