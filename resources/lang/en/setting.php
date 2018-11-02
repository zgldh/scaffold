<?php

return [
    'title'       => 'Setting',
    'fields'      =>
        [
            'name'          => 'Name',
            'value'         => 'Value',
            'type'          => 'Type',
            'settable_id'   => 'Settable Id',
            'settable_type' => 'Settable Type',
        ],
    'bundles'     =>
        [
            'system' =>
                [
                    'site_name'         => 'Site Name',
                    'site_introduction' => 'Site Introduction',
                    'default_language'  => 'Default Language',
                    'target_planets'    =>
                        [
                            '_name' => 'Target Planets',
                            'earth' => 'Earth',
                            'mars'  => 'Mars',
                            'sun'   => 'Sun',
                            'moon'  => 'Moon',
                        ],
                ],
        ],
    'permissions' =>
        [
            'reset'      => 'Reset system settings',
            'update_all' => 'Update system settings',
        ],
];
