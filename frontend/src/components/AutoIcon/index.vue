<template>
  <div :class="className" class="auto-icon">
    <svg-icon v-if="iconType === TYPE_SVGICON" :icon-class="iconClass"></svg-icon>
    <i v-if="iconType === TYPE_FONTAWESOME" :class="'fa '+iconClass"></i>
    <i v-if="iconType === TYPE_OTHERS" :class="iconClass"></i>
  </div>
</template>

<script type="javascript">
  const TYPE_SVGICON = 1;
  const TYPE_FONTAWESOME = 2;
  const TYPE_OTHERS = 3;

  export default {
    name: 'auto-icon',
    props: {
      iconClass: {
        type: String,
        required: true
      },
      className: {
        type: String
      }
    },
    data(){
      return {
        TYPE_SVGICON: 1,
        TYPE_FONTAWESOME: 2,
        TYPE_OTHERS: 3,
      };
    },
    computed: {
      iconType(){
        if (this.iconClass.indexOf('fa-') === 0) {
          return TYPE_FONTAWESOME;
        }
        else if (this.iconClass.indexOf('el-') === 0) {
          return TYPE_OTHERS;
        }
        else if (this.iconClass.indexOf('ion-') === 0) {
          return TYPE_OTHERS;
        }
        return TYPE_SVGICON;
      },
    }
  }


</script>

<style lang="scss" scoped>
  .auto-icon {
    display: inline-block;
    text-align: center;
  }
</style>
