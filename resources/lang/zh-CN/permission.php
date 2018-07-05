<?php

return [
    'title'           => '权限',
    'fields'          =>
        [
            'name'  => '内部名',
            'label' => '显示名',
        ],
    'default_actions' =>
        [
            'index'   => '列表',
            'show'    => '详情',
            'store'   => '添加',
            'update'  => '编辑',
            'destroy' => '删除',
        ],
    'permissions'     =>
        [
            'sync_roles' => '配置对应的角色',
        ],
];
