<template>
  <div class="simple-layout">
    <transition name="fade" mode="out-in">
      <!-- <router-view :key="key"></router-view> -->
      <router-view></router-view>
    </transition>
  </div>
</template>

<script type="javascript">

  export default {
    name: 'simple',
    components: {},
    computed: {}
  }


</script>

<style rel="stylesheet/scss" lang="scss">
  @import "../../styles/variables";

  .simple-layout {
    position: fixed;
    height: 100%;
    width: 100%;
    background-color: $subMenuBg;
    color: $borderL4;
    input:-webkit-autofill {
      -webkit-box-shadow: 0 0 0px 1000px $menuBg inset !important;
      -webkit-text-fill-color: $borderL4 !important;
    }
    input {
      background-color: lighten($subMenuBg, 10%);
      -webkit-appearance: none;
      padding: 12px 5px 12px 15px;
      color: $borderL3;
    }
    .el-input {
      display: inline-block;
      width: 85%;
    }
    .tips {
      font-size: 14px;
      color: $borderL4;
      margin-bottom: 10px;
    }
    .svg-container {
      padding: 6px 5px 6px 15px;
      color: $textSecondary;
      vertical-align: middle;
      width: 30px;
      display: inline-block;
    }
    .title {
      font-size: 26px;
      color: $borderL3;
      margin: 0px auto 40px auto;
      text-align: center;
      font-weight: bold;
    }
    .main-form {
      position: absolute;
      left: 0;
      right: 0;
      width: 400px;
      padding: 35px 35px 15px 35px;
      margin: 120px auto;
    }
    .form-item {
      border: 1px solid $borderL4;
      background: $borderL1;
      color: $textPrimary;
    }
    .show-pwd {
      position: absolute;
      right: 10px;
      top: 7px;
      font-size: 16px;
      color: $textSecondary;
      cursor: pointer;
      user-select: none;
    }
    .thirdparty-button {
      position: absolute;
      right: 35px;
      bottom: 28px;
    }
    i.fa.fa-envelope {
      padding: 6px 5px 6px 15px;
      color: $textSecondary;
      width: 30px;
    }
  }
</style>
