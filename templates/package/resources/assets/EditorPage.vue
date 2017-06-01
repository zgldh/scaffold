<template>
  <div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>$MODEL_NAME_PRESENTATION$
        <small v-if="item.id">编辑$MODEL_NAME_PRESENTATION$</small>
        <small v-else>新建$MODEL_NAME_PRESENTATION$</small>
      </h1>
      <ol class="breadcrumb">
        <li>
          <router-link to="/"><i class="fa fa-dashboard"></i> 总览</router-link>
        </li>
        <li>
          <router-link to="/$MODEL_NAME_LOWER$">$MODEL_NAME_PRESENTATION$管理</router-link>
        </li>
        <li class="active" v-if="item.id">编辑$MODEL_NAME_PRESENTATION$</li>
        <li class="active" v-else>新建$MODEL_NAME_PRESENTATION$</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="box box-primary">
        <!--<div class="box-header with-border">-->
        <!--</div>-->
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">

          <form class="form-horizontal" @submit="onSave">
            <div class="form-group" v-if="item.id">
              <label for="field-id" class="col-sm-2 control-label">ID</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="field-id" v-model="item.id" disabled>
              </div>
            </div>

            $FORM_FIELDS$

            <div class="form-group" v-if="item.id">
              <label for="field-created_at" class="col-sm-2 control-label">创建时间</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="field-created_at" v-model="item.created_at" disabled>
              </div>
            </div>
          </form>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
          <router-link-back class="btn btn-lg btn-flat btn-default pull-left">返回</router-link-back>
          <button type="submit" form="editing-form" class="btn btn-lg btn-flat btn-primary" @click="onSave"
                  :disabled="saving">
            {{saving?"保存中...":"保存"}}
          </button>
        </div>
      </div>

    </section>
    <!-- /.content -->
  </div>
</template>

<script type="javascript">
  import {Vue} from 'resources/assets/js/commons/vuejs.js';
  import {alert} from 'resources/assets/js/components/SweetAlertDialogs';
  import ErrorsBuilder from 'resources/assets/js/commons/ErrorsBuilder.js';

  var resourceURL = "/$MODEL_NAME_LOWER$";
  var resource = Vue.resource(resourceURL + '{/id}');
  // var resource = Vue.resource(resourceURL + '{/id}?_with=permissions,roles'); // 载入额外的关联数据(relationships)
  var vueConfig = {
    data: function () {
      var data = {
        item: $APP_PAGE_EMPTY_ITEMS$,
        saving: false
      };
      data.item.$errors = ErrorsBuilder();
      return data;
    },
    beforeRouteEnter (to, from, next) {
      if (to.params.id) {
        resource.get({id: to.params.id}).then(function (result) {
          next(function (vm) {
            vm.item = result.data.data;
            vm.item.$errors = ErrorsBuilder();
          })
        }).catch(function (err) {
          next(false);
        });
      }
      else {
        next();
      }
    },
    methods: {
      onSave: function (event) {
        this.saving = true;
        this.item.$errors.removeAll();

        var promise = null;
        let payload = $.extend(true, {}, this.item);
        if (payload.id) {
          promise = resource.update({id: payload.id}, payload).then(function (result) {
            window.toastr["success"]("编辑已保存");
            return result.data.data;
          });
        }
        else {
          promise = resource.save(payload).then(function (result) {
            window.toastr["success"]("新增成功");
            return result.data.data;
          });
        }

        promise.then(function (data) {
          return resource.get({id: data.id});
        }).then(function (result) {
          this.saving = false;
          this.item = result.data.data;
          this.item.$errors = ErrorsBuilder();
        }.bind(this)).catch(function (err) {
          this.saving = false;
          if (err.status == 422) {
            this.item.$errors.setAll(err.body);
            this.item.$errors.focusFirstErrorField();
          }
          else {
            alert(err.data.message);
          }
        }.bind(this));

        return false;
      },
    }
  };
  export default vueConfig;

</script>

<style lang="scss">

</style>
