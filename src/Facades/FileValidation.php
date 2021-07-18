<?php

namespace App\Leonardo\Facades;

/**
 * Trait FileValidation
 * @package App\Leonardo\Facades
 *
 * @property array $files;
 * @property boolean $multiple = false;
 * @property string $path = './';
 * @property string $paramName = 'file';
 * @property boolean $generateFilename = true;
 *
 * @method get($filename)
 * @method save($param = '')
 * @method processamento(File $file)
 * @method multipleFiles($on = true)
 * @method parameter($param)
 * @method path($relativePath)
 * @method getPath()
 * @method filename($filename)
 * @method getFilename($originalName, $extension)
 * @method exists($filename)
 *
 */
trait FileValidation {

    /**
     * @var array
     */
    private $errorMessages = array(
        0 => 'O arquivo enviado excede a diretiva upload_max_filesize em php.ini',
        1 => 'O arquivo enviado excede o tamanho de arquivo estabelecido de :filesize',
        2 => 'O arquivo carregado foi carregado apenas parcialmente',
        3 => 'Nenhum arquivo foi enviado',
        4 => 'Faltando uma pasta temporária',
        5 => 'Falha ao gravar arquivo no disco',
        6 => 'Uma extensão PHP interrompeu o upload do arquivo',
        7 => 'Extensão inválida para o arquivo :name',
    );

    /**
     * @var array
     */
    private $rules = [
        'size'  => null,
        'type'  => [],
    ];

    /** @var array */
    private $errors = [];

    /**
     * @param array $rules
     * @param array $messages
     * @return $this
     */
    public function addValidations(array $rules, $messages = []) {
        $this->rules            = array_merge($this->rules, $rules);
        $this->errorMessages    = array_merge($this->errorMessages, $messages);

        return $this;
    }


    /**
     * @return bool
     */
    public function validate() {
        $validate = true;

        $this->files = new File($this->paramName);

        if($this->multiple || $this->files->hasMultipleFiles()) {

            foreach($this->files->getInstanceMultiple() as $item) {
                $this->isValid($item);
            }

        } else {
            $this->isValid($this->files);
        }

        return (count($this->errors()) === 0);
    }


    /**
     * @return array
     */
    public function errors() {
        return $this->errors;
    }


    /**
     * @param File $file
     * @return array
     */
    private function isValid(File $file) {

        // ++ validação da extensão
        if ($this->validateExtension($file->getOriginalExtension()) === true) {
            $this->addError(7, 'name', $file->getOriginalName());
        }

        // ++ validação do tamanho do arquivo
        if($this->validateFilesize($file->getSize()) === true) {
            $this->addError(1, 'filesize', $this->rules['size']);
        }

        return $this->errors();
    }


    /**
     * @param $extension
     * @return bool
     */
    private function validateExtension($extension) {
        $rule = is_array($this->rules['type']) ?
            $this->rules['type'] : [$this->rules['type']];

        return ((count($rule) > 0) && !in_array($extension, $rule));
    }


    /**
     * @param $filesize
     * @return bool
     */
    private function validateFilesize($filesize) {
        return (
            !empty($this->rules['size'])
            &&
            ($filesize <= $this->convertToBytes($this->rules['size']))
        );
    }


    /**
     * @param int $code
     * @param string $key
     * @param null   $value
     * @return array
     */
    private function addError($code, $key = 'key', $value = null) {
        array_push($this->errors,
            str_replace(":{$key}", $value, $this->errorMessages[$code])
        );

        return $this->errors();
    }


    /**
     * @return int|string
     */
    public function kbytes() {
        $size = $this->getSize();

        if ($size >= 1073741824) {
            $size = number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            $size = number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            $size = number_format($size / 1024, 2) . ' KB';
        } elseif ($size > 1) {
            $size = "{$size} bytes";
        } elseif ($size == 1) {
            $size = "{$size} byte";
        } else {
            $size = '0 bytes';
        }

        return $size;
    }


    /**
     * @param $from
     * @return float|int|string|string[]|null
     */
    private function convertToBytes($from) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $number = substr($from, 0, -2);
        $suffix = strtoupper(substr($from,-2));

        //B or no suffix
        if(is_numeric(substr($suffix, 0, 1))) {
            return preg_replace('/[^\d]/', '', $from);
        }

        $exponent = array_flip($units)[$suffix] ?? null;

        return ($exponent === null) ?
            null :
            ($number * (1024 ** $exponent));
    }

}
