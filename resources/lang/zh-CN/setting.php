<?php
return [
    'title'   => '设置',
    'fields'  => [
        'name'          => '内部编号',
        'value'         => '取值',
        'type'          => '类型',
        'settable_id'   => '可配置对象ID',
        'settable_type' => '可配置对象类型',
    ],
    'bundles' => [
        'system' => [
            'site_name'         => '站点名称',
            'site_introduction' => '站点介绍',
            'default_language'  => '默认语言',
            'target_planets'    => [
                '_name' => '目标星球',
                'earth' => '地球',
                'mars'  => '火星',
                'sun'   => '太阳',
                'moon'  => '月球',
            ]
        ]
    ]
];
