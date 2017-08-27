<?php
/**
 * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
 */
?>
{{'@'}}extends('admin.index')

{{'@'}}section('title',"{{$MODEL_NAME}}")

{{'@'}}section('content')
{{'@'}}endsection

{{'@'}}section('scripts')
    <script>
        {{-- 如果有的话，在此输出前端立即需要的数据 --}}
    </script>
    {{'@'}}dist('vendor')
{{'@'}}endsection
