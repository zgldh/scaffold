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
   * @param inputDefaultItem
   * @returns {{props: {}, components: {BaseFormDialog}, computed: {}, methods: {onDelete: defaultVueConfig.methods.onDelete, onCreate: defaultVueConfig.methods.onCreate, onEdit: defaultVueConfig.methods.onEdit}, watch: {}}}
   */
  buildVueConfig: function (resourceURL, datatablesConfig, inputDefaultItem, openDialog) {
    var resource = null;
    var appPageTable = null;
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
          resourceURL: resourceURL,
          defaultItem: inputDefaultItem
        };
      },
      components: {},
      computed: {
        selectedItems: {
          get (){
            return this.$store.state.selectedItems;
          }
        }
      },
      methods: {
        onDelete: function (id) {
          confirm("真的要删除吗？").then(data => {
            return resource.delete({id: id});
          }, function (data) {
            // 'cancel' || 'overlay'
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
          var me = this;
          item = item ? item : this.defaultItem;
          this.$refs.editorDialog.open(this.defaultItem)
            .then(function ({item, closeDialog, showError}) {
              let payload = trimObjects(Object.assign({}, item));
              for (let key in payload) {
                if (payload[key] == null) {
                  payload[key] = me.defaultItem[key];
                }
              }

              resource.save(payload)
                .then((result) => {
                  return resource.get({id: result.body.data.id});
                })
                .then((result) => {
                  appPageTable.row.add(result.body.data).draw();
                  window.toastr["success"]("新增成功");
                  closeDialog();
                })
                .catch(function (err) {
                  showError(err);
                });
            });
        },
        onEdit: function (itemId) {
          var me = this;
          let item = appPageTable.row('#' + itemId).data();
          var defaultItem = Object.assign({}, this.defaultItem);
          for (let key in defaultItem) {
            if (item[key] != null) {
              defaultItem[key] = item[key];
            }
          }

          this.$refs.editorDialog.open(defaultItem)
            .then(function ({item, closeDialog, showError}) {
              let payload = trimObjects(Object.assign({}, item));
              for (let key in payload) {
                if (payload[key] == null) {
                  payload[key] = me.defaultItem[key];
                }
              }

              resource.update({id: payload.id}, payload)
                .then((result) => {
                  return resource.get({id: result.body.data.id});
                })
                .then((result) => {
                  appPageTable.row('#' + itemId).data(result.body.data).draw('full-hold');
                  window.toastr["success"]("编辑已保存");
                  closeDialog();
                })
                .catch(function (err) {
                  showError(err);
                });
            });
        },
        onBundle: function (action, options) {
          var selectedItems = Object.assign([], this.selectedItems);
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
          });
        }
      }, // end: methods
      watch: {},
      mounted: function () {
        appPageTable = $('#app-page-table');
        appPageTable = appPageTable.DataTable(datatablesConfig);
        let me = this;
        appPageTable.on('click', 'button.edit-item-btn', function () {
          let itemId = $(this).data('item-id');
          me.onEdit(itemId);
        });
        appPageTable.on('click', 'button.delete-item-btn', function () {
          let itemId = $(this).data('item-id');
          me.onDelete(itemId);
        });
        appPageTable.on('select', function (e, dt, type, indexes) {
          let items = dt.rows('.selected').data().toArray();
          me.$store.commit('setSelectedItems', items);
        });
        appPageTable.on('deselect', function (e, dt, type, indexes) {
          let items = dt.rows('.selected').data().toArray();
          me.$store.commit('setSelectedItems', items);
        });
        if (openDialog) {
          me.onCreate();
        }
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
    config.methods = Object.assign({
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
    return config;
  }
};

export default vueHelper;

// Store functions
const store = new Vuex.Store({
  state: {
    selectedItems: []
  },
  mutations: {
    setSelectedItems: function (state, items) {
      state.selectedItems = items;
    }
  },
  actions: {}
});
export {store};

function trimObjects (item) {
  for (let key in item) {
    if (item.hasOwnProperty(key)) {
      if (typeof(item[key]) == 'object' && item[key] !== null) {
        if (item[key].hasOwnProperty('id')) {
          // item[key] = item[key].id; //FIXME 需要重新考虑
          let ObjectInfo = Object.assign({}, item[key]);
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