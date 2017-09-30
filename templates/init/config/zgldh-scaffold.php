<?php
return [
    /**
     * The root directory name for all modules of scaffold
     */
    'modules'   => 'Modules',

    /**
     * Templates to create a new module from a starter.
     */
    'templates' => [
        'model'            => [
            'zgldh.scaffold::raw.Model',    // The template file location
            'Models/$MODEL_NAME$.php'       // The generated file location. $MODEL_NAME$ is in PascalCase.
        ],
        'repository'       => [
            'zgldh.scaffold::raw.Repository',
            'Repositories/$MODEL_NAME$Repository.php'
        ],
        'controller'       => [
            'zgldh.scaffold::raw.Controller',
            'Controllers/$MODEL_NAME$Controller.php'
        ],
        'request'          => [
            'create' => [
                'zgldh.scaffold::raw.Requests.Create',
                'Requests/Create$MODEL_NAME$Request.php'
            ],
            'update' => [
                'zgldh.scaffold::raw.Requests.Update',
                'Requests/Update$MODEL_NAME$Request.php'
            ],
        ],
        'resource'         => [
            'vue'    => [
                'list'   => [
                    'zgldh.scaffold::raw.resources.assets.ListPage',
                    'resources/assets/$MODEL_NAME$/ListPage.vue'
                ],
                'editor' => [
                    'zgldh.scaffold::raw.resources.assets.EditorPage',
                    'resources/assets/$MODEL_NAME$/EditorPage.vue'
                ],
                'store'  => [
                    'zgldh.scaffold::raw.resources.assets.store',
                    'resources/assets/$MODEL_NAME$/store.js'
                ],
            ],
            'views'  => [
                'index' => [
                    'zgldh.scaffold::raw.resources.views.index',
                    'resources/views/$MODEL_NAME$/index.blade.php'
                ],
            ],
            'routes' => [
                'zgldh.scaffold::raw.resources.assets.routes',
                'resources/assets/routes.js'
            ],
        ],
        'routes'           => [
            'zgldh.scaffold::raw.routes',
            'routes.php'
        ],
        'service_provider' => 'zgldh.scaffold::raw.ServiceProvider',
        'menu'             => 'zgldh.scaffold::raw.menuItem',
    ],
];
