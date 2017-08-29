@php
    /**
     * @var $STARTER \zgldh\Scaffold\Installer\ModuleStarter
     * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
     * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
     */
$models = $STARTER->getModels();
$moduleTitle = $STARTER->getModuleTitle();
$moduleName = $STARTER->getModuleName();
@endphp

<?php echo "{{-- Module: {$moduleTitle} {$moduleName} --}}"; ?>

@if(sizeof($models)>1)
<router-treeview title="{{$moduleTitle}}管理" icon="fa fa-circle-o" :match="['/{{snake_case($moduleName)}}']">
<?php foreach($models as $MODEL):
        $route = $MODEL->getRoute();
        $modelTitle = $MODEL->getTitle();
        ?>
    <router-link tag="li" to="/{{$route}}/list">
        <a><i class="fa fa-circle-o"></i> <span>{{$modelTitle}}</span></a>
    </router-link>
<?php endforeach; ?>
</router-treeview>
@else
@php
    $MODEL = array_pop($models);
    $route = $MODEL->getRoute();
    $modelTitle = $MODEL->getTitle();
@endphp
<router-link tag="li" to="/{{$route}}/list" exact>
    <a><i class="fa fa-circle-o"></i><span>{{$modelTitle}}</span></a>
</router-link>
@endif