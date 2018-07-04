<?php namespace Modules\Upload;

use zgldh\UploadManager\UploadStrategy as BaseStrategy;

class UploadStrategy extends BaseStrategy
{
    /**
     * 得到 disk public 内上传的文件的URL
     * @param $path
     * @return string
     */
    public function getPublicUrl($path)
    {
        $url = url('storage/' . $path);
        return $url;
    }
}