<?php

return [
    'title'       => 'Notification',
    'fields'      =>
        [
            'type'    => 'Type',
            'data'    => 'Data',
            'read_at' => 'Read At',
        ],
    'permissions' =>
        [
        ],
    'types'       =>
        [
            'Modules\\Notification\\Notifications\\Bar' => 'Bar Title',
            'Modules\\Notification\\Notifications\\Foo' => 'Foo Title',
        ],
];
