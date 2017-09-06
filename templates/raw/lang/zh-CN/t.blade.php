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
<?php foreach($STARTER->getModels() as $MODEL):
    $fieldLabels = $MODEL->getFieldLabels();
?>
        '{{$MODEL->getSnakeCase()}}'=>[
            'title'=>'{{$MODEL->getPascaleCase()}}',
            'fields'=>{!! \zgldh\Scaffold\Installer\Utils::exportArray($fieldLabels) !!}
        ],
<?php endforeach;?>
    ]
];
