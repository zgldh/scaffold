<template>
  <div class="advance-search-date">
    <div class="advance-search-date-inner">
      <label>{{name}}:</label>
      <el-date-picker v-model="selectedValue"
                      :type="Type" align="right"
                      :default-time="DefaultTime">
      </el-date-picker>
    </div>
    <el-button type="text" icon="el-icon-close" :size="size" circle
               @click="onClose"></el-button>
  </div>
</template>

<script type="javascript">
  import { updateSearchParams } from '@/utils/addressbar';
  import { UnifiedValue, SerializerDatatablesParameters } from '@/utils/datatables';
  import moment from 'moment'

  export default {
    name: 'advance-search-date',
    components: {},
    props: {
      name: {
        type: String,
      },
      field: {
        type: String,
        required: true
      },
      size: {
        type: String,
      },
      parameters: {
        type: Object,
        required: true
      },
      initValues: null,
    },
    data(){
      return {
        selectedValue: '',
        operator: '='
      }
    },
    computed: {
      Type() {
        if (this.parameters.hasOwnProperty('Type')) {
          return this.parameters.Type;
        }
        return 'date';
      },
      DefaultTime() {
        if (this.parameters.hasOwnProperty('DefaultTime')) {
          return this.parameters.DefaultTime;
        }
        return '00:00:00';
      },
      isRange(){
        return this.Type.indexOf('range') >= 0;
      }
    },
    watch: {
      selectedValue(newValue){
        var start = null;
        var end = null;
        if (newValue.constructor === Array) {
          start = moment(newValue[0]);
          end = moment(newValue[1]);
        }
        else {
          start = moment(newValue);
        }
        var searches = this.getSearches(start, end);

        if (newValue.constructor === Array) {
          updateSearchParams(this.field, [
            start.format('X'), end.format('X')
          ])
        }
        else {
          updateSearchParams(this.field, start.format('X'));
        }
        this.$emit('column-changed', searches);
      }
    },
    mounted(){
      this.init();
    },
    methods: {
      init(){
        if (this.initValues) {
          if (this.initValues.constructor === Array) {
            this.selectedValue = [
              moment.unix(this.initValues[0]).toDate(),
              moment.unix(this.initValues[1]).toDate(),
            ];
          }
          else {
            this.selectedValue = moment.unix(this.initValues).toDate();
          }
        }
      },
      onClose(){
        this.$emit('column-close', {
          fieldName: this.field
        });
      },
      getSearches(start, end){
        start = start.clone();
        end = end ? end.clone() : null;
        var searches = null;
        switch (this.Type) {
          case 'year':
          case 'month':
            searches = [
              {
                fieldName: this.field,
                operator: '>=',
                value: start.format('YYYY-MM-DD')
              }, {
                fieldName: this.field,
                operator: '<',
                value: start.add(1, this.Type).format('YYYY-MM-DD')
              }
            ];
            break;
          case 'date':
            searches = [
              {
                fieldName: this.field,
                operator: '>=',
                value: start.format('YYYY-MM-DD')
              }, {
                fieldName: this.field,
                operator: '<',
                value: start.add(1, 'day').format('YYYY-MM-DD')
              }
            ];
            break;
          case 'week':
            searches = [
              {
                fieldName: this.field,
                operator: '>=',
                value: start.add(-1, 'day').format('YYYY-MM-DD')
              }, {
                fieldName: this.field,
                operator: '<',
                value: start.add(7, 'day').format('YYYY-MM-DD')
              }
            ];
            break;
          case 'datetime':
            searches = {
              fieldName: this.field,
              operator: '=',
              value: start.format('YYYY-MM-DD HH:mm:ss')
            };
            break;
          case 'datetimerange':
            searches = [
              {
                fieldName: this.field,
                operator: '>=',
                value: start.format('YYYY-MM-DD HH:mm:ss')
              }, {
                fieldName: this.field,
                operator: '<=',
                value: end.format('YYYY-MM-DD HH:mm:ss')
              }
            ];
            break;
          case 'daterange':
            searches = [
              {
                fieldName: this.field,
                operator: '>=',
                value: start.format('YYYY-MM-DD')
              }, {
                fieldName: this.field,
                operator: '<=',
                value: end.format('YYYY-MM-DD')
              }
            ];
            break;
        }
        return searches;
      }
    }
  }
</script>

<style lang="scss">
  @import "../../../styles/variables.scss";

  .advance-search-date {
    .advance-search-date-inner {
      font-size: 13px;
      line-height: normal;
      border-collapse: separate;
      display: inline-table;
      vertical-align: middle;
      label {
        background-color: $borderL4;
        color: $textSecondary;
        vertical-align: middle;
        display: table-cell;
        position: relative;
        border: 1px solid $borderL1;
        padding: 0 20px;
        width: 1px;
        white-space: nowrap;

        border-right: 0;
        border-radius: 4px 0 0 4px;
      }
      > .el-date-editor {
        display: table-cell;
        &, & > input {
          border-radius: 0 4px 4px 0;
        }
      }
    }
  }
</style>
