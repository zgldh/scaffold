<template>
  <div class="advance-search-drop-down">
    <div class="advance-search-drop-down-inner">
      <label>{{name}}:</label>
      <el-select v-model="selectedValue"
                 :multiple="multiple"
                 :size="size">
        <el-option
                v-for="item in items" :key="item.Value"
                :label="getColumnName(item.Title)"
                :value="item.Value">
        </el-option>
      </el-select>
    </div>
    <el-button type="text" icon="el-icon-close" :size="size" circle
               @click="onClose"></el-button>
  </div>
</template>

<script type="javascript">
  import { updateSearchParams } from '@/utils/addressbar';
  import { UnifiedValue, SerializerDatatablesParameters } from '@/utils/datatables';
  import _ from 'lodash'
  import CellActions from '@/components/CellActions'

  export default {
    name: 'advance-search-drop-down',
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
        /**
         *  {
         *    Multiple: true|false,
         *    Items: [
         *      {
         *        Title: 'Foo',
         *        Value: 123,                 // It makes 123==field
         *        Value: 'interval:[123,456]'  // It makes 123<=field AND field<=456
         *        Value: 'interval:(123,456]'  // It makes 123<field AND field<=456
         *        Value: 'interval:[123,456)'  // It makes 123<=field AND field<456
         *        Value: 'interval:(123,456)'  // It makes 123<field AND field<456
         *        Value: 'interval:(123,Infinity)'  // It makes 123<field
         *      },
         *      ...
         *    ]
         *  },
         **/
        type: Object,
        required: true
      },
      initValues: null
    },
    data(){
      const data = {
        selectedValue: this.parameters.Multiple ? [] : null,
        operator: '='
      }
      return data;
    },
    computed: {
      multiple(){
        return this.parameters.Multiple || false;
      },
      items(){
        if (this.parameters.hasOwnProperty('Items')) {
          return this.parameters.Items;
        }
        return [
          {
            Title: () => this.$i18n.t('global.terms.yes'),
            Value: true
          },
          {
            Title: () => this.$i18n.t('global.terms.no'),
            Value: false
          }
        ];
      }
    },
    watch: {
      selectedValue(newValue, oldValue){
        this.$emit('column-clear', { fieldName: this.field });

        updateSearchParams(this.field, newValue)
        var startValue, endValue, startOperator, endOperator;
        [startValue, endValue, startOperator, endOperator] = this.splitIntervalStatement(newValue)
        if (startValue && endValue) {
          var searches = [
            {
              fieldName: this.field,
              operator: startOperator,
              value: startValue
            }
          ];
          if (endValue !== 'Infinity') {
            searches.push({
              fieldName: this.field,
              operator: endOperator,
              value: endValue
            });
          }
          this.$emit('column-changed', searches);
        } else {
          this.$emit('column-changed', {
            fieldName: this.field,
            operator: this.operator,
            value: newValue
          });
        }
      }
    },
    mounted(){
      this.init();
    },
    methods: {
      init(){
        this.initValuesProcess();
      },
      initValuesProcess(){
        if (this.initValues !== null && this.initValues !== undefined) {
          if (this.parameters.Multiple) {
            if (this.initValues.constructor !== Array) {
              this.selectedValue = [this.initValues];
              return true;
            }
          }
          this.selectedValue = this.initValues;
        }
      },
      handleSelect(item){
        this.selectedValue = item.Value;
        this.selectedText = this.getColumnName(item.Title);
      },
      getColumnName(name){
        if (name.constructor === Function) {
          return name();
        }
        return name;
      },
      onClose(){
        this.$emit('column-close', {
          fieldName: this.field
        });
      },
      splitIntervalStatement(rawString){
        var startValue, endValue, startOperator, endOperator;
        var keyword = 'interval:'
        var operatorsMap = {
          '[': '>=',
          '(': '>',
          ')': '<',
          ']': '<='
        }
        if (rawString.indexOf(keyword) === 0) {
          var regExp = /(\[|\()(.*),(.*)(\]|\))/;
          var execResult = regExp.exec(rawString.substr(8));
          if (execResult !== null) {
            [rawString, startOperator, startValue, endValue, endOperator] = execResult;
            startOperator = operatorsMap[startOperator];
            endOperator = operatorsMap[endOperator];
          }
        }
        return [startValue, endValue, startOperator, endOperator];
      }
    }
  }
</script>

<style lang="scss">
  @import "../../../styles/variables.scss";

  .advance-search-drop-down {
    .advance-search-drop-down-inner {
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
      .el-select {
        display: table-cell;
        width: 200px;
        input {
          border-radius: 0 4px 4px 0;
        }
      }
    }
  }
</style>
