<template>
  <vue-select v-bind:value="computedValue"
              :options="computedOptions"
              :taggable="taggable"
              :on-change="onChange"
              :on-search="onSearch"
              :label="label"
              :multiple="multiple"
              :debounce="250"
  ></vue-select>
</template>

<script type="javascript">
  import vSelect from 'vue-select';

  export default  {
    props: {
      value: {
        default: null
      },
      label: null,
      multiple: null,
      options: {
        type: Array,
        default() {
          return []
        },
      },
      taggable: {
        type: Boolean,
        default: false
      },
      onSearch: {
        type: Function,
        default() {
          return null;
        },
      },
      valueKey: {
        type: String,
        default() {
          return "value"
        },
      }
    },
    data: function () {
      return {};
    },
    components: {
      'vue-select': vSelect
    },
    watch: {
      value: function (value) {
      },
    },
    computed: {
      computedValue: function () {
        var vm = this;
        var result = null;
        var trimmedValue = this.trimValue(this.value);
        if (this.multiple && trimmedValue.constructor == Array) {
          result = this.options.filter(function (option) {
            return trimmedValue.indexOf(option[vm.valueKey]) >= 0;
          });
        }
        else {
          result = this.options.find(function (option) {
            return option[vm.valueKey] == trimmedValue;
          });
        }
        return result;
      },
      computedOptions: function () {
        var vm = this;
        return this.options.map(function (option) {
          option.value = option[vm.valueKey];
          return option;
        });
      }
    },
    methods: {
      onChange: function (val) {
        var vm = this;
        var newVal = this.trimValue(val);
        var oldVal = this.trimValue(this.value);
        if (newVal.toString() == oldVal.toString()) {
          return;
        }
        this.$emit('input', newVal);
      },
      trimValue: function (value) {
        if (!value) {
          return this.multiple ? [] : "";
        }
        if (this.multiple && value.constructor == Array) {
          return value.map(function (item) {
            return this.trimValue(item);
          }.bind(this));
        }
        else if (value.hasOwnProperty(this.valueKey)) {
          return value[this.valueKey];
        }
        else {
          return value;
        }
      }
    }
  };

</script>
