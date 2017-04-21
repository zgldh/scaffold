<template>
  <a class="full-screen-toggle" v-if="isEnabled" @click="toggle">
    <span class="glyphicon glyphicon-fullscreen"></span>
  </a>
</template>

<script type="javascript">

  export default  {
    data: function () {
      return {
        isEnabled: false,
        element: null,
        fullScreenTarget: null
      };
    },
    mounted: function () {
      let vm = this;
      this.isEnabled = window.BigScreen.enabled;

      window.BigScreen.onenter = function (element) {
        $(element).addClass('full-screen');
        vm.element = element;
      };
      window.BigScreen.onexit = function () {
        $(vm.element).removeClass('full-screen');
        vm.element = null;
      };
    },
    destroyed: function () {
    },
    methods: {
      toggle: function () {
        var element = $('.full-screen-target');
        if (element.size()) {
          element = element[0];
        }
        else {
          element = document.getElementById('content-wrapper');
        }
        window.BigScreen.request(element);
      }
    }
  };
</script>

<style lang="scss">
  #content-wrapper.full-screen, .full-screen-target.full-screen {
    width: 100%;
    height: 100%;
    min-height: 100% !important;
    margin: 0 !important;
    padding: 0;
  }

  .full-screen-toggle {
    cursor: pointer;
    color: white;
    float: left;
    background-color: transparent;
    background-image: none;
    padding: 15px 15px;

    &:hover {
      background-color: #367fa9;
      color: #f6f6f6;
    }
  }
</style>

