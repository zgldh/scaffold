<?php
return [
    /**
     * The root directory name for all modules of scaffold
     */
    'modules'   => '$NAME$',

    /**
     * Templates to create a new module from a starter.
     */
    'templates' => [
        'model'            => 'zgldh.scaffold::raw.Model',
        'repository'       => 'zgldh.scaffold::raw.Repository',
        'controller'       => 'zgldh.scaffold::raw.Controller',
        'request'          => [
            'create' => 'zgldh.scaffold::raw.Requests.Create',
            'update' => 'zgldh.scaffold::raw.Requests.Update',
        ],
        'resource'         => [
            'vue'    => [
                'list'   => 'zgldh.scaffold::raw.resources.assets.ListPage',
                'editor' => 'zgldh.scaffold::raw.resources.assets.EditorPage',
                'store'  => 'zgldh.scaffold::raw.resources.assets.store',
            ],
            'views'  => [
                'index' => 'zgldh.scaffold::raw.resources.views.index',
            ],
            'routes' => 'zgldh.scaffold::raw.resources.assets.routes'
        ],
        'routes'           => 'zgldh.scaffold::raw.routes',
        'menu'             => 'zgldh.scaffold::raw.menuItem',
        'service_provider' => 'zgldh.scaffold::raw.ServiceProvider',
    ]
];
