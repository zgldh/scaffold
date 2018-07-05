<template>
  <el-dropdown trigger="click" class='international' @command="handleSetLanguage">
    <div>
      <svg-icon class-name='international-icon' icon-class="language"/>
    </div>
    <el-dropdown-menu class="international-menu" slot="dropdown">
      <el-dropdown-item command="zh-CN" :disabled="language==='zh-CN'">中文
      </el-dropdown-item>
      <el-dropdown-item command="en" :disabled="language==='en'">English
      </el-dropdown-item>
    </el-dropdown-menu>
  </el-dropdown>
</template>

<script type="javascript">
  export default {
    computed: {
      language() {
        return this.$store.getters.language
      }
    },
    methods: {
      handleSetLanguage(lang) {
        this.$i18n.locale = lang
        this.$store.dispatch('setLanguage', lang)
        this.$message({
          message: this.$t('components.lang_select.complete_text'),
          type: 'success'
        })
      }
    }
  }
</script>

<style lang="scss">
  @import "../../styles/variables.scss";

  .international-icon {
    font-size: 20px;
    cursor: pointer;
    vertical-align: -5px !important;
  }

  .international-menu {
    .el-dropdown-menu__item.is-disabled {
      color: $blue;
    }
  }
</style>

