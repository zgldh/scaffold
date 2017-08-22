<?php
/**
 * @var $STARTER \zgldh\Scaffold\Installer\ModuleStarter
 * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
 */
echo '<?php' ?>

return [
    'title' => "{{$STARTER->getModuleName()}}",
    'models'=>[
        @foreach($STARTER->getModels() as $MODEL)
        '{{$MODEL->getSnakeCase()}}'=>[
            'title'=>'{{$MODEL->getPascaleCase()}}'
        ],
        @endforeach
    ]
];
