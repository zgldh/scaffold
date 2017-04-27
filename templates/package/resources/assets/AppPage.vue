<template>
  <div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>$MODEL_NAME_PRESENTATION$
        <small>列表</small>
      </h1>
      <ol class="breadcrumb">
        <li>
          <router-link to="/"><i class="fa fa-dashboard"></i> 控制台</router-link>
        </li>
        <li><a href="#">$MODEL_NAME_PRESENTATION$</a></li>
        <li class="active">列表</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="box box-primary">
        <div class="box-header with-border">
          <button type="button" class="btn btn-flat btn-primary" @click="onCreate"><i class="fa fa-plus"></i> 新增
          </button>
          <button type="button" class="btn btn-danger btn-flat" :disabled="!selectedItems.length"
                  @click="onBundleDelete"><i
                  class="fa fa-trash"></i> 批量删除
          </button>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
          <div class="table-responsive">
            <table id="app-page-table" class="table table-striped table-bordered" cellspacing="0" width="100%"></table>
          </div>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
          <button type="button" class="btn btn-flat btn-primary" @click="onCreate"><i class="fa fa-plus"></i> 新增
          </button>
          <button type="button" class="btn btn-danger btn-flat" :disabled="!selectedItems.length"
                  @click="onBundleDelete"><i
                  class="fa fa-trash"></i> 批量删除
          </button>
        </div>
      </div>

    </section>
    <!-- /.content -->
  </div>
</template>

<script type="javascript">
  import {Vue} from 'resources/assets/js/commons/vuejs.js';
  import datatablesHelper from 'resources/assets/js/commons/datatables-helper.js';
  import vueHelper from 'resources/assets/js/commons/vuejs.js';
  import columns from './TableColumns';

  let resourceURL = "/$MODEL_NAME_LOWER$";
  let datatablesConfig = datatablesHelper.buildDatatablesConfig({
    resource: resourceURL,
    // with: ['roles', 'permissions'] // 载入额外的关联数据(relationship)
  }, columns);
  let vueConfig = vueHelper.buildVueConfig(datatablesConfig);
  vueConfig.data = function () {
    return {
      resourceURL: resourceURL, // 前台新建页、编辑页跳转、删除AJAX调用
    };
  };

  export default vueConfig;
</script>

<style lang="scss">

</style>
