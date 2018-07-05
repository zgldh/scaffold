<?php

return [
    'title'       => 'User',
    'fields'      =>
        [
            'name'          => 'Name',
            'email'         => 'Email',
            'password'      => 'Password',
            'is_active'     => 'Is Active',
            'last_login_at' => 'Last Login At',
            'gender'        => 'Gender',
        ],
    'terms'       =>
        [
            'male'   => 'Male',
            'female' => 'Female',
        ],
    'permissions' =>
        [
            'current'      => 'current',
            'post_avatar'  => 'post_avatar',
            'put_gender'   => 'put_gender',
            'put_mobile'   => 'put_mobile',
            'put_password' => 'put_password',
        ],
];
