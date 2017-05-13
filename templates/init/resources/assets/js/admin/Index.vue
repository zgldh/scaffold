<template>
  <div>
    <div>this is template body {{msg}}</div>
  </div>
</template>

<script type="javascript">
  export default{
    data: function () {
      return {
        msg: 'hello vue. good luck',
        slider: 10,
        radio: 1,
        form: {
          name: '',
          region: '',
          date1: '',
          date2: '',
          delivery: false,
          type: [],
          resource: '',
          desc: ''
        }
      };
    }
  }
</script>
<style lang="scss">

</style>
