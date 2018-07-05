<?php

return [
    'modules' => 'Modules',

    'default_middleware' => 'auth:api',

    'frontend_folder' => 'frontend',

    /**
     * Templates to create a new module from a starter.
     */
    'templates'       => [
        'model'            => [
            'scaffold::raw.Model',    // The template file location
            'Models/$MODEL_NAME$.php'       // The generated file location. $MODEL_NAME$ is in PascalCase.
        ],
        'repository'       => [
            'scaffold::raw.Repository',
            'Repositories/$MODEL_NAME$Repository.php'
        ],
        'controller'       => [
            'scaffold::raw.Controller',
            'Controllers/$MODEL_NAME$Controller.php'
        ],
        'request'          => [
            'create' => [
                'scaffold::raw.Requests.Create',
                'Requests/Create$MODEL_NAME$Request.php'
            ],
            'update' => [
                'scaffold::raw.Requests.Update',
                'Requests/Update$MODEL_NAME$Request.php'
            ],
        ],
        'factory'          => [
            'scaffold::factory',    // The template file location
        ],
        'routes'           => [
            'scaffold::raw.routes',
            'routes.php'
        ],
        'frontend'         => [
            'pages'  => [
                'list'   => [
                    'scaffold::frontend.List',
                    base_path('frontend/src/views/$MODULE_NAME$/$MODEL_NAME$/List.vue')
                ],
                'editor' => [
                    'scaffold::frontend.Editor',
                    base_path('frontend/src/views/$MODULE_NAME$/$MODEL_NAME$/Editor.vue')
                ]
            ],
            'routes' => [
                'scaffold::frontend.routes',
                base_path('frontend/src/router/dynamicRouterMap.js')
            ],
        ],
        'service_provider' => 'scaffold::raw.ServiceProvider',
        'menu'             => 'scaffold::raw.menuItem',
    ],
];