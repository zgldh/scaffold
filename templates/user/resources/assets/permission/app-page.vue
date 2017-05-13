<template>
  <div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>用户管理
        <small>权限</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> 控制台</a></li>
        <li><a href="#">用户管理</a></li>
        <li class="active">权限</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="box box-primary">
        <div class="box-header with-border">
          <button type="button" class="btn btn-flat btn-primary" @click="onCreate"><i class="fa fa-plus"></i> 新增
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
        </div>
      </div>

    </section>
    <!-- /.content -->

    <form-dialog></form-dialog>
  </div>
</template>

<script type="javascript">
  import Vue from 'vue';
  import datatablesHelper from 'resources/assets/js/commons/datatables-helper.js';
  import vueHelper from 'resources/assets/js/commons/vuejs.js';
  import columns from './table-columns';

  let emptyItem = {
    id: null,
    name: ''
  };
  var appPageTable = null;
  var resourceURL = "/user/permission";
  var datatablesConfig = datatablesHelper.buildDatatablesConfig(resourceURL, columns);
  var vueConfig = vueHelper.buildVueConfig(resourceURL, datatablesConfig, emptyItem);

  export default vueConfig;

  // Form Dialog definition
  let formDialog = vueHelper.buildFormDialogConfig({
    props: ['item'],
    template: require('./form-fields.html')
  });
  Vue.component('form-dialog', formDialog);

</script>

<style lang="scss">

</style>
