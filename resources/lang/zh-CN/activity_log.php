<?php

return [
    'title'       => '操作日志',
    'fields'      =>
        [
            'log_name'       => '日志名',
            'description'    => '描述',
            'subject_id'     => '主题ID',
            'subject_type'   => '主题类型',
            'causer_id'      => '操作者ID',
            'causer_type'    => '操作者类型',
            'properties'     => '变化',
            'collector_type' => '集合类型',
            'collector_id'   => '集合ID',
            'created_at'     => '发生时间',
        ],
    'terms'       => [
        'description_search' => '动作'
    ],
    'type'        => [
        'login'            => '登录',
        'logout'           => '退出',
        'created'          => '创建了',
        'updated'          => '更新了',
        'deleted'          => '删除了',
        'updated-password' => '更新了密码',
    ],
    'permissions' =>
        [
        ],
];
