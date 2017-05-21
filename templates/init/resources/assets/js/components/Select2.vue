<template>
  <select>
    <slot></slot>
  </select>
</template>

<script type="javascript">
  export default  {
    props: ['options', 'value'],
    mounted: function () {
      let vm = this;
      $(this.$el)
      // init select2
              .select2({
                data: this.options,
                language: 'zh-CN'
              })
              // emit event on change.
              .on('change', function () {
                let thisValue = $(this).val();
                if (thisValue && thisValue.constructor == Array) {
                  for (let i = 0; i < thisValue.length; i++) {
                    thisValue[i] = parseInt(thisValue[i]);
                  }
                }
                else {
                  thisValue = parseInt(thisValue);
                  if (isNaN(thisValue)) {
                    thisValue = null;
                  }
                }
                vm.$emit('input', thisValue);
              })
              .val(this.value).trigger('change');
    },
    watch: {
      value: function (value) {
        // update value
        let obj = $(this.$el);
        let oldValue = obj.val();

        if (oldValue === undefined || oldValue == null) {
          oldValue = [];
        }
        if (value === undefined || value == null) {
          value = [];
        }

        let valueLen = value.length;
        for (let i = 0; i < valueLen; i++) {
          let item = value[i];
          if (typeof(item) == 'object') {
            value[i] = item.id;
          }
        }

        if (oldValue.toString() !== value.toString()) {
          obj.val(value).trigger("change");
        }
      },
      options: function (options) {
        // update options
        let select = $(this.$el);
        select.empty();
        select.select2({data: options});
        if (options.constructor == Array && options.length) {
          this.$emit('input', options[0].id);
        }
        else {
          this.$emit('input', null);
        }
      }
    },
    destroyed: function () {
      $(this.$el).off().select2('destroy');
    }
  };

</script>
