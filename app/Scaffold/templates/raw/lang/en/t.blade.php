<?php
/**
 * @var $STARTER \App\Scaffold\Installer\ModelStarter
 * @var $MODEL   \App\Scaffold\Installer\Model\ModelDefinition
 * @var $field   \App\Scaffold\Installer\Model\Field
 */
$MODEL = $STARTER->getModel();
$fieldLabels = $MODEL->getFieldLabels('en');
echo '<?php' ?>

return [
    'title'=>'{{$MODEL->getPascaleCase()}}',
    'fields'=>{!! \App\Scaffold\Installer\Utils::exportArray($fieldLabels) !!}
];
