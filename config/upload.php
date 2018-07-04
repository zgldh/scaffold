<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2015/7/23
 * Time: 16:52
 */

return [

    /**
     * 请在filesystems.php 的 disks 数组里挑一个
     **/
    'base_storage_disk' => 'public',

    /**
     * 对应的数据模型类
     **/
    'upload_model'      => \Modules\Upload\Models\Upload::class,

    /**
     * 上传策略类
     **/
    'upload_strategy'   => \Modules\Upload\UploadStrategy::class,

    /**
     * validator group 用于 withValidator()函数 common是默认的。
     **/
    'validator_groups'  => [
        'common' => [
            /**
             * 请参考 http://laravel.com/docs/5.5/validation
             **/
            'min' => 0,  //kilobytes    
            'max' => 4096,  //kilobytes
        ],
        'image'  => [
            'max'   => 8192,  //kilobytes
            'mimes' => 'jpeg,bmp,png,gif'
        ]
    ]
];