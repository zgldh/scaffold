@php
    /**
     * @var $STARTER \App\Scaffold\Installer\ModuleStarter
     * @var $MODEL \App\Scaffold\Installer\Model\ModelDefinition
     * @var $field  \App\Scaffold\Installer\Model\FieldDefinition
     */
$models = $STARTER->getModels();
$moduleTitle = $STARTER->getModuleTitle();
$moduleName = $STARTER->getModuleName();
$moduleSnakeCase = snake_case($moduleName);
@endphp

<?php echo "{{-- Module: {$moduleTitle} {$moduleName} --}}"; ?>

@if(sizeof($models)>1)
<router-treeview title="<?php echo "@lang('".$moduleSnakeCase."::t.title')"; ?>" icon="fa fa-circle-o" :match="['/{{snake_case($moduleName)}}']">
<?php foreach($models as $MODEL):
        $route = $MODEL->getRoute();
        $modelTitle = $MODEL->getTitle();
        $modelSnakeCase = $MODEL->getSnakeCase();
        ?>
    <router-link tag="li" to="/{{$route}}/list">
        <a><i class="fa fa-circle-o"></i> <span><?php echo "@lang('".$moduleSnakeCase."::t.models.".$modelSnakeCase.".title')"; ?></span></a>
    </router-link>
<?php endforeach; ?>
</router-treeview>
@else
@php
    $MODEL = array_pop($models);
    $route = $MODEL->getRoute();
    $modelSnakeCase = $MODEL->getSnakeCase();
@endphp
<router-link tag="li" to="/{{$route}}/list" exact>
    <a><i class="fa fa-circle-o"></i><span><?php echo "@lang('".$moduleSnakeCase."::t.models.".$modelSnakeCase.".title')"; ?></span></a>
</router-link>
@endif