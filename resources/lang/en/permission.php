<?php

return [
    'title'           => 'Permission',
    'fields'          =>
        [
            'name'  => 'Name',
            'label' => 'Label',
        ],
    'default_actions' =>
        [
            'index'   => 'List',
            'show'    => 'Detail',
            'store'   => 'Create',
            'update'  => 'Edit',
            'destroy' => 'Delete',
        ],
    'permissions'     =>
        [
            'sync_roles' => 'Sync to Roles',
        ],
];
