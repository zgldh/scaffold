<template>
  <li class="treeview" v-bind:class="treeviewClassObject">
    <a href="#">
      <i v-bind:class="iconClassObject"></i>
      <span>{{title}}</span>
      <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu" v-bind:class="treeviewMenuClassObject" v-bind:style="displayStyle">
      <slot></slot>
    </ul>
  </li>
</template>

<script type="javascript">

  export default  {
    props: {
      match: {
        type: [String, Array],
        default: ''
      },
      title: String,
      icon: {
        type: String,
        default: 'fa fa-pencil-square-o'
      }
    },
    data: function () {
      return {};
    },
    computed: {
      matchesReg: function () {
        var matches = this.match;
        if (this.match.construct == String) {
          matches = [matches];
        }
        return '^' + matches.join('|');
      },
      matched: function () {
        return this.$route.fullPath.match(this.matchesReg) !== null;
      },
      iconClassObject: function () {
        var obj = {};
        obj[this.icon] = true;
        return obj;
      },
      treeviewClassObject: function () {
        return {'active': this.matched};
      },
      treeviewMenuClassObject: function () {
        return {'menu-open': this.matched};
      },
      displayStyle: function () {
        if (this.matched) {
          return {display: 'block'};
        }
        return null;
      }
    },
    mounted: function () {
    },
    destroyed: function () {
    },
    methods: {}
  };
</script>

<style lang="scss">
</style>

