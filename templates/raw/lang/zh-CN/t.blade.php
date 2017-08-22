<?php
/**
 * @var $STARTER \zgldh\Scaffold\Installer\ModuleStarter
 * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
 */
echo '<?php' ?>

return [
    'title' => "{{$STARTER->getModuleTitle()}}管理",
    'models'=>[
        @foreach($STARTER->getModels() as $MODEL)
        '{{$MODEL->getSnakeCase()}}'=>[
            'title'=>'{{$MODEL->getTitle()}}'
        ],
        @endforeach
    ]
];
