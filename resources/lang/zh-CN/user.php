<?php

return [
    'title'       => '用户',
    'fields'      =>
        [
            'name'          => '用户名',
            'email'         => '电子邮箱',
            'password'      => '密码',
            'is_active'     => '是否可用',
            'last_login_at' => '上次登录时间',
            'gender'        => '性别',
        ],
    'terms'       =>
        [
            'male'   => '男',
            'female' => '女',
        ],
    'permissions' =>
        [
            'current'      => '读取当前用户',
            'post_avatar'  => '上传头像',
            'put_gender'   => '更新性别',
            'put_mobile'   => '更新手机',
            'put_password' => '更新密码',
        ],
];
