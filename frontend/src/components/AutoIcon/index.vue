<template>
  <div :class="className" class="auto-icon" @click="onClick">
    <svg-icon v-if="iconType === TYPE_SVGICON" :icon-class="iconClass"></svg-icon>
    <i v-if="iconType === TYPE_FONTAWESOME" :class="iconClass+' fa'"></i>
    <i v-if="iconType === TYPE_IONICON" :class="iconClass+' icon'"></i>
    <i v-if="iconType === TYPE_OTHERS" :class="iconClass"></i>
  </div>
</template>

<script type="javascript">
  const TYPE_SVGICON = 1;
  const TYPE_FONTAWESOME = 2;
  const TYPE_IONICON = 3;
  const TYPE_OTHERS = 4;

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
        TYPE_SVGICON: TYPE_SVGICON,
        TYPE_FONTAWESOME: TYPE_FONTAWESOME,
        TYPE_IONICON: TYPE_IONICON,
        TYPE_OTHERS: TYPE_OTHERS,
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
          return TYPE_IONICON;
        }
        return TYPE_SVGICON;
      },
    },
    methods: {
      onClick(e){
        this.$emit('click', e)
      }
    }
  }


</script>

<style lang="scss" scoped>
  .auto-icon {
    display: inline-block;
    text-align: center;
  }
</style>
