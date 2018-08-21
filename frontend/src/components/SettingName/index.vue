<template>
  <span class="setting-name">{{translatedName}}</span>
</template>

<script type="javascript">
  import setting_name from '@/utils/setting'

  export default {
    name: 'setting-label',
    props: {
      name: String,
      bundle: String
    },
    data() {
      return {}
    },
    computed: {
      translatedName() {
        return setting_name(this.bundle, this.name);
      }
    },
    methods: {}
  }
</script>

<style lang="scss" scoped>
  .setting-name {
  }
</style>
