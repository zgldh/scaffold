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
      <el-dropdown-menu slot="dropdown" v-if="availableColumns.length>0">
        <el-dropdown-item v-for="(column, $index) in availableColumns"
                          :key="$index"
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
    data() {
      return {
        usedFields: [],
        columnsInUse: []
      }
    },
    computed: {
      availableColumns() {
        return this.columns.filter(column => {
          return this.usedFields.indexOf(column.Field) === -1;
        });
      }
    },
    mounted() {
      this.init();
    },
    watch: {
      columns(newValue) {
        this.columnsInUse.forEach(column => {
          var updatedColumn = newValue.find(item => item.Field === column.Field);
          if (updatedColumn) {
            column.Name = updatedColumn.Name;
            column.Type = updatedColumn.Type;
            column.Component = updatedColumn.Component;
            column.ComponentParameters = updatedColumn.ComponentParameters;
          }
        });
      }
    },
    methods: {
      init() {
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
      getColumnName(name) {
        if (name.constructor === Function) {
          return name();
        }
        return name;
      },
      getColumnInitValues(field) {
        return getSearchParams(field);
      },
      handleAddSearch(action) {
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
      onColumnChanged(searches) {
        this.$emit('filter-search', searches);
      },
      onColumnClear({ fieldName, operator }) {
        removeSearchParams(fieldName);
        this.$emit('filter-remove', {
          fieldName: fieldName,
          operator: operator
        });
      },
      onColumnClose({ fieldName, operator }) {
        this.usedFields.splice(this.usedFields.findIndex(field => field === fieldName), 1);
        this.columnsInUse.splice(this.columnsInUse.findIndex(column => column.Field === fieldName), 1);

        removeSearchParams(fieldName);
        this.$emit('filter-remove', {
          fieldName: fieldName,
          operator: operator
        });
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
