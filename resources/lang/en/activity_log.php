<?php

return [
    'title'       => 'Activity Log',
    'fields'      =>
        [
            'log_name'       => 'Log Name',
            'description'    => 'Description',
            'subject_id'     => 'Subject Id',
            'subject_type'   => 'Subject Type',
            'causer_id'      => 'Causer Id',
            'causer_type'    => 'Causer Type',
            'properties'     => 'Properties',
            'collector_type' => 'Collector Type',
            'collector_id'   => 'Collector Id',
            'created_at'     => 'Occurs At',
        ],
    'terms'       => [
        'description_search' => 'Action'
    ],
    'type'        => [
        'login'            => 'Login',
        'logout'           => 'Logout',
        'created'          => 'Created',
        'updated'          => 'Updated',
        'deleted'          => 'Deleted',
        'updated-password' => 'Updated the password of',
    ],
    'permissions' =>
        [
        ],
];
