<template>
  <div class="advance-search-text-input">
    <el-input v-model="input" placeholder="请输入内容" :size="size">
      <template slot="prepend">{{name}}</template>
    </el-input>
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
    name: 'advance-search-text-input',
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
         *    Like: false|true
         *  },
         **/
        type: Object,
        required: true
      },
      initValues: null
    },
    data(){
      return {
        input: "",
        operator: this.parameters.Like ? 'like' : '='
      }
    },
    computed: {},
    watch: {
      input: _.debounce(function (newValue, oldValue) {
        updateSearchParams(this.field, this.input)
        this.$emit('column-changed', {
          fieldName: this.field,
          operator: this.operator,
          value: this.input
        });
      }, 500),
    },
    mounted(){
      this.init();
    },
    methods: {
      init(){
        if (this.initValues !== null) {
          this.input = this.initValues;
        }
      },
      onClose(){
        this.$emit('column-close', {
          fieldName: this.field,
          operator: this.operator
        });
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../../styles/variables.scss";

  .advance-search-text-input {
    .el-input {
      width: 300px;
    }
    input {
      border-color: $info;
    }
    .el-input-group__prepend {
      background: $info;
      border-color: $info;
      color: white;
    }
  }
</style>
