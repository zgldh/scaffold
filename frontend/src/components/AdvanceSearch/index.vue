<template>
  <div class="advance-search">
    <div class="columns-in-use">
      <component class="column-in-use" v-for="(column, $index) in columnsInUse"
                 :key="$index"
                 :name="getColumnName(column.Name)"
                 :field="column.Field"
                 :size="size"
                 :parameters="column.ComponentParameters"
                 :init-values="getColumnInitValues(column.Field)"
                 @column-changed="onColumnChanged"
                 @column-clear="onColumnClear"
                 @column-close="onColumnClose"
                 v-bind:is="column.Component"></component>
    </div>
    <el-dropdown @command="handleAddSearch" :size="size" :hide-on-click="false">
      <el-button type="info" :size="size" :disabled="availableColumns.length ===0">
        {{$t('components.advance_search.add_button')}}<i
              class="el-icon-plus el-icon--right"></i>
      </el-button>
      <el-dropdown-menu slot="dropdown">
        <el-dropdown-item v-for="(column, $index) in availableColumns" :key="$index"
                          :command="column">
          {{getColumnName(column.Name)}}
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
  </div>
</template>

<script type="javascript">
  import {
    removeSearchParams,
    getSearchParams,
    PARAMS_SEARCH_PREFIX,
  } from '@/utils/addressbar';
  import { UnifiedValue, SerializerDatatablesParameters } from '@/utils/datatables';
  import _ from 'lodash'

  var TypeToComponent = {
    String: 'TextInput',
    Boolean: 'DropDown',
    Select: 'DropDown',
    Date: 'Date',
    Time: 'Date', // TODO
  };

  export default {
    name: 'advance-search',
    components: {
      'TextInput': () => import('./Components/TextInput.vue'),
      'DropDown': () => import('./Components/DropDown.vue'),
      'Date': () => import('./Components/Date.vue'),
    },
    props: {
      /**
       * [
       *  {
       *    Name: () => this.$i18n.t('user.fields.last_login_at'),
       *    Field: 'last_login_at',
       *    Type: Date,
       *    Component: 'DateTimeRange',
       *    ComponentParameters: {
       *      Min: "2012-12-12",
       *      Max: "2013-10-22",
       *    }
       *  },
       *  {...}
       * ]
       **/
      columns: {
        type: Array,
        required: true
      },
      autoSearch: {
        type: Boolean,
        default: false
      },
      size: {
        type: String,
        default: ''
      }
    },
    data(){
      return {
        usedFields: [],
        columnsInUse: []
      }
    },
    computed: {
      availableColumns(){
        return this.columns.filter(column => {
          return this.usedFields.indexOf(column.Field) === -1;
        });
      }
    },
    mounted(){
      this.init();
    },
    watch: {},
    methods: {
      init(){
        _.forEach(this.$route.query, (value, key) => {
          if (key.indexOf(PARAMS_SEARCH_PREFIX) !== -1) {
            var fieldName = key.substr(PARAMS_SEARCH_PREFIX.length);
            var column = this.columns.find(item => {
              return item.Field === fieldName
            });
            if (column) {
              this.handleAddSearch(column);
            }
            else {
              removeSearchParams(fieldName);
            }
          }
        })
      },
      getColumnName(name){
        if (name.constructor === Function) {
          return name();
        }
        return name;
      },
      getColumnInitValues(field){
        return getSearchParams(field);
      },
      handleAddSearch(action){
        var column = _.clone(action);
        if (column.hasOwnProperty('Component') === false) {
          column.Component = TypeToComponent[column.Type.constructor === String ? column.Type : column.Type.name];
        }
        if (column.hasOwnProperty('ComponentParameters') === false) {
          column.ComponentParameters = {};
        }
        this.usedFields.push(column.Field);
        this.columnsInUse.push(column);
      },
      onColumnChanged(searches){
        this.$emit('filter-search', searches);
      },
      onColumnClear({ fieldName, operator }){
        removeSearchParams(fieldName);
        this.$emit('filter-remove', {
          fieldName: fieldName,
          operator: operator
        });
      },
      onColumnClose({ fieldName, operator }){
        this.usedFields.splice(this.usedFields.findIndex(field => field === fieldName), 1);
        this.columnsInUse.splice(this.columnsInUse.findIndex(column => column.Field === fieldName), 1);

        removeSearchParams(fieldName);
        this.$emit('filter-remove', {
          fieldName: fieldName,
          operator: operator
        });
      },

      buildSearchParameters: function () {
        return;// TODO buildSearchParameters
        if (!this.$refs.searchForm) {
          return;
        }
        var searchComponents = FindSearchComponents(this.$refs.searchForm);
        searchComponents.forEach(item => {
          var columnName = item.$el.getAttribute('column')
          if (!columnName) {
            return;
          }
          var operator = item.$el.getAttribute('operator') ? item.$el.getAttribute('operator') : '=';
          let value = null;
          let accessor = item.$el.getAttribute('accessor');
          if (accessor) {
            value = _.get(this.searchForm, accessor);
          } else {
            // TODO: Access currentValue for elemenr ui component
            value = this.searchForm.hasOwnProperty(columnName) ? this.searchForm[columnName] : _.get(this.searchForm, columnName);
          }

          if (value === '' || value === null || value === undefined) {
            this.clearAdvanceSearchToColumn(columnName, operator)
          } else {
            switch (operator) {
              case 'range':
                if (value[0]) {
                  this.applyAdvanceSearchToColumn(columnName, '>=', UnifiedValue(value[0]))
                } else {
                  this.clearAdvanceSearchToColumn(columnName, '>=')
                }
                if (value[1]) {
                  this.applyAdvanceSearchToColumn(columnName, '<=', UnifiedValue(value[1], true))
                } else {
                  this.clearAdvanceSearchToColumn(columnName, '<=')
                }
                break
              case 'like':
                this.applyAdvanceSearchToColumn(columnName, operator, '%' + UnifiedValue(value) + '%')
                break
              default:
                // >, >=, =, <=, <
                this.applyAdvanceSearchToColumn(columnName, operator, UnifiedValue(value))
                break
            }
          }
        })
      }
    }
  }
</script>

<style lang="scss">
  .advance-search {
    .columns-in-use {
      display: inline-block;
      .column-in-use {
        display: inline-block;
        margin-right: 1em;
        &:hover, &:hover * {
          font-weight: bolder;
        }
      }
    }
  }
</style>
