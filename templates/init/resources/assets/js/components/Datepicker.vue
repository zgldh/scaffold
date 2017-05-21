<template>
    <input type="text" value="">
</template>

<script type="javascript">

    export default  {
        props: ['value'],
        mounted: function () {
            let vm = this;
            $(this.$el).datepicker({
                language: 'zh-CN',
                format: 'yyyy-mm-dd'
            }).on('changeDate', function (e) {
                vm.$emit('input', $(this).val());
            });
        },
        watch: {
            value: function (value) {
                // update value
                let obj = $(this.$el);
                let oldValue = obj.val();

                if (value == null && oldValue != null) {
                    obj.datepicker('clearDates');
                }
                else if (oldValue.toString() !== value.toString()) {
                    obj.datepicker('setDate', value);
                }
            }
        },
        destroyed: function () {
            $(this.$el).off().datepicker('destroy');
        }
    };


</script>
