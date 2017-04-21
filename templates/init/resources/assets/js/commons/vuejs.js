import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router'
Vue.use(Vuex);
Vue.use(VueRouter);

export {Vue, Vuex};

import './resource';
import BaseFormDialog from '../components/BaseFormDialog.vue';
Vue.component('base-form-dialog', BaseFormDialog);
import vSelect from '../components/Select.vue';
Vue.component('v-select', vSelect);
import Datepicker from '../components/Datepicker.vue';
Vue.component('datepicker', Datepicker);
import UploadFile from '../components/UploadFile.vue';
Vue.component('upload-file', UploadFile);
import FullScreenButton from '../components/FullScreenButton.vue';
Vue.component('full-screen-button', FullScreenButton);
import RouterTreeview from '../components/RouterTreeview.vue';
Vue.component('router-treeview', RouterTreeview);


import { confirm, alert } from '../components/SweetAlertDialogs';

let appPageTable = null;

const vueHelper = {
  /**
   * 生成 VueJS 配置对象
   * @param datatablesConfig
   * @returns {{props: {}, components: {BaseFormDialog}, computed: {}, methods: {onDelete: defaultVueConfig.methods.onDelete, onCreate: defaultVueConfig.methods.onCreate, onEdit: defaultVueConfig.methods.onEdit}, watch: {}}}
   */
  buildVueConfig: function (datatablesConfig) {
    let config = {
      props: {},
      components: {},
      computed: {
        selectedItems: {
          get (){
            return this.$store.state.selectedItems;
          }
        },
        appPageTable: function () {
          return appPageTable;
        }
      },
      methods: {
        onDelete: function (id) {
          confirm("真的要删除吗？").then(function (data) {
			  return this.$http.delete(this.resourceURL + '/'+id);
          }.bind(this)).catch(function (err) {
            if (err !== "overlay") {
              alert(err.data.message);
            }
          }).then(response => {
            if (response) {
              appPageTable.row('#' + id).remove().draw('full-hold');
              window.toastr["success"]("已删除");
            }
          });
        },
        onBundleDelete: function () {
          var me = this;
          confirm("真的要删除 " + this.selectedItems.length + " 项吗？")
            .then(data => me.onBundle('delete'))
            .then((data) => window.toastr["success"]("已删除"));
        },
        onCreate: function (item) {
          return this.$router.push(this.resourceURL + '/create');
        },
        onEdit: function (itemId) {
          return this.$router.push(this.resourceURL + '/' + itemId + '/edit');
        },
        onBundle: function (action, options) {
          var selectedItems = $.extend([], this.selectedItems);
          return this.$http.post(this.resourceURL + '/bundle', {
            action: action,
            indexes: selectedItems.map((item) => item.id),
            options: options
          }).then(response => {
            if (response && response.ok) {
              appPageTable.draw('full-hold');
              return response.data;
            }
            else {
              throw new Error(response.data);
            }
          }).catch(function (err) {
            alert(err.data.message);
            throw err;
          });
        }
      }, // end: methods
      watch: {},
      mounted: function () {
        appPageTable = $('#app-page-table');
        appPageTable = appPageTable.DataTable(datatablesConfig);
        let me = this;
        appPageTable.on('click', 'button', function (e) {
          var button = $(this);
          if (button.is('.edit-item-btn')) {
            let itemId = $(this).data('item-id');
            me.onEdit(itemId);
          }
          else if (button.is('.delete-item-btn')) {
            let itemId = $(this).data('item-id');
            me.onDelete(itemId);
          }
          else {
            var onClick = button.attr('v-on:click');
            if (onClick != undefined) {
              var {functionName, parameters} = splitFunction(onClick);
              if (!parameters) {
                parameters = [e];
              }
              var callback = me[functionName];
              if (callback && callback.constructor == Function) {
                callback.apply(me, parameters);
              }
            }
          }
        });
        appPageTable.on('select', function (e, dt, type, indexes) {
          let items = dt.rows('.selected').data().toArray();
          me.$store.commit('setSelectedItems', items);
        });
        appPageTable.on('deselect', function (e, dt, type, indexes) {
          let items = dt.rows('.selected').data().toArray();
          me.$store.commit('setSelectedItems', items);
        });
      }
    };

    return config;
  },
  /**
   * 生成表单对话框配置对象
   * @param config
   * @returns {*}
   */
  buildFormDialogConfig: function (config) {
    config.methods = $.extend({
      onFileChange: function (val) {
        this.item[val.target.attributes.item('name').value] = val.target.files[0];
      },
      open: function (item) {
        return this.$refs.baseDialog.open(item);
      },
      close: function () {
        return this.$refs.baseDialog.close();
      },
    }, config.methods);
    config.computed = $.extend({
      item: function () {
        return this.$refs.baseDialog.item;
      },
    }, config.computed);
    return config;
  }
};

function splitFunction (str) {
  var functionPattern = /^[^(]+(\((.*)\))?/g;
  var result = functionPattern.exec(str);
  var parameters = result[2].split(',');
  var functionName = result[0].replace(result[1], '');
  var buttonContent = {
    functionName: functionName,
    parameters: parameters
  }
  return buttonContent;
}
export default vueHelper;

export function trimObjects (item) {
  for (let key in item) {
    if (item.hasOwnProperty(key)) {
      if (typeof(item[key]) == 'object' && item[key] !== null) {
        if (item[key].hasOwnProperty('id')) {
          // item[key] = item[key].id; //FIXME 需要重新考虑
          let ObjectInfo = $.extend({}, item[key]);
          for (let prop in ObjectInfo) {
            if (item[key].hasOwnProperty(prop)) {
              if (typeof ObjectInfo[prop] !== 'object') {
                item[key][prop] = ObjectInfo[prop];
              }
            }
          }
        }
      }
    }
  }
  return item;
}
export function TrimSelections (selections) {
  for (var i = 0, len = selections.length; i < len; i++) {
    var selection = selections[i];
    if (selection.hasOwnProperty('value')) {
      selections[i] = selection.value;
    }
    else if (selection.hasOwnProperty('id')) {
      selections[i] = selection.id;
    }
  }
  return selections;
}