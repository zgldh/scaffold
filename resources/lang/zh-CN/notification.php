<?php

return [
    'title'       => '通知',
    'fields'      =>
        [
            'type'    => '类型',
            'data'    => '内容',
            'read_at' => '阅读于',
        ],
    'permissions' =>
        [
        ],
    'types'       =>
        [
            'Modules\\Notification\\Notifications\\Bar' => 'Bar标题',
            'Modules\\Notification\\Notifications\\Foo' => 'Foo标题',
        ],
];
