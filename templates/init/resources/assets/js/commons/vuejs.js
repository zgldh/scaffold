import NProgress from 'nprogress'
import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router'
Vue.use(Vuex);
Vue.use(VueRouter);

export {Vue, Vuex};

import './resource';
import BaseFormDialog from '../components/BaseFormDialog.vue';
Vue.component('base-form-dialog', BaseFormDialog);
import Select2 from '../components/Select2.vue';
Vue.component('select2', Select2);
import Datepicker from '../components/Datepicker.vue';
Vue.component('datepicker', Datepicker);
import UploadFile from '../components/UploadFile.vue';
Vue.component('upload-file', UploadFile);
import FullScreenButton from '../components/FullScreenButton.vue';
Vue.component('full-screen-button', FullScreenButton);

import {confirm, alert} from '../components/SweetAlertDialogs';


const vueHelper = {
  /**
   * 生成 VueJS 配置对象
   * @param resourceURL
   * @param datatablesConfig
   * @returns {{props: {}, components: {BaseFormDialog}, computed: {}, methods: {onDelete: defaultVueConfig.methods.onDelete, onCreate: defaultVueConfig.methods.onCreate, onEdit: defaultVueConfig.methods.onEdit}, watch: {}}}
   */
  buildVueConfig: function (resourceURL, datatablesConfig) {
    let resource = null;
    let advanceColumnSearch = {};
    let appPageTable = null;

    if (resourceURL.constructor == Object) {
      var url = resourceURL.resource + '{/id}';
      url += resourceURL.with ? "?_with=" + encodeURIComponent(resourceURL.with) : '';
      resource = Vue.resource(url);
    }
    else {
      resource = Vue.resource(resourceURL + '{/id}');
    }
    let config = {
      props: {},
      data: function () {
        return {
          resourceURL: resourceURL
        };
      },
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
        searchColumn: function (columnName, searchTerm, operator) {
          operator = operator ? operator : '=';
          advanceColumnSearch[columnName] = advanceColumnSearch[columnName] ? advanceColumnSearch[columnName] : {};
          if (searchTerm == "" || searchTerm == null || searchTerm == undefined) {
            delete advanceColumnSearch[columnName][operator];
          }
          else {
            advanceColumnSearch[columnName][operator] = searchTerm;
          }
          return this.appPageTable;
        },
        onDelete: function (id) {
          confirm("真的要删除吗？").then(function (data) {
            return resource.delete({id: id});
          }).catch(function (err) {
            if (err !== "overlay") {
              alert(err.data.message);
            }
          }).then(response => {
            if (response) {
              this.appPageTable.row('#' + id).remove().draw('full-hold');
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
              this.appPageTable.draw('full-hold');
              return response.data;
            }
            else {
              throw new Error(response.data);
            }
          }).catch(function (err) {
            alert(err.data.message);
            throw err;
          });
        },
        setPageAndLength: function (page, length) {
          page = Math.max(0, parseInt(page));
          length = Math.max(10, parseInt(length));
          var info = this.appPageTable.page.info();
          if (info.page != page || info.length != length) {
            this.appPageTable.page(page);
            this.appPageTable.page.len(length).draw();
          }
        }
      }, // end: methods
      watch: {
        '$route.query': function (query) {
          this.setPageAndLength(query.page - 1, query.pageSize);
        }
      },
      mounted: function () {
        if (this.$route.query.hasOwnProperty('pageSize')) {
          datatablesConfig.pageLength = parseInt(this.$route.query.pageSize);
          datatablesConfig.iDisplayLength = datatablesConfig.pageLength;
        }
        if (this.$route.query.hasOwnProperty('page')) {
          datatablesConfig.displayStart = (this.$route.query.page - 1) * datatablesConfig.pageLength;
          datatablesConfig.iDisplayStart = datatablesConfig.displayStart;
        }

        appPageTable = $('#app-page-table').DataTable(datatablesConfig);
        let me = this;
        this.appPageTable.on('click', 'button', function (e) {
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
        this.appPageTable.on('preXhr', function (e, settings, json) {
          for (var columnName in advanceColumnSearch) {
            var searches = advanceColumnSearch[columnName];
            var columnIndex = json.columns.findIndex((column) => {
              return column.name == columnName;
            });
            if (columnIndex !== -1 && searches) {
              json.columns[columnIndex].search.advance = advanceColumnSearch[columnName];
            }
          }
          NProgress.start();
        });
        this.appPageTable.on('xhr', function (e, settings, json, xhr) {
          NProgress.done();
        });
        this.appPageTable.on('select', function (e, dt, type, indexes) {
          let items = dt.rows('.selected').data().toArray();
          me.$store.commit('setSelectedItems', items);
        });
        this.appPageTable.on('deselect', function (e, dt, type, indexes) {
          let items = dt.rows('.selected').data().toArray();
          me.$store.commit('setSelectedItems', items);
        });
        this.appPageTable.on('length', function (e, settings, len) {
          var info = me.appPageTable.page.info();
          me.$router.push({
            path: me.$route.path,
            query: {page: info.page + 1, pageSize: info.length}
          });
        });
        this.appPageTable.on('page', function (e, settings) {
          var info = me.appPageTable.page.info();
          me.$router.push({
            path: me.$route.path,
            query: {page: info.page + 1, pageSize: info.length}
          });
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

export function TrimObjects (item) {
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