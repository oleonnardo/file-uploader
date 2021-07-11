<?php

if(! function_exists('uploader')) {
    /**
     * @return \App\Leonardo\FileUploader
     */
    function uploader() {
        return new \App\Leonardo\FileUploader();
    }

}
