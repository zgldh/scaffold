<template>
  <el-breadcrumb class="app-breadcrumb" separator="/">
    <transition-group name="breadcrumb">
      <el-breadcrumb-item v-for="(item,index)  in levelList" :key="item.path"
                          v-if="renderTitle(item.meta.title)">
        <span v-if="item.redirect==='noredirect'||index==levelList.length-1"
              class="no-redirect">{{renderTitle(item.meta.title)}}</span>
        <router-link v-else :to="item.redirect||item.path">
          {{renderTitle(item.meta.title)}}
        </router-link>
      </el-breadcrumb-item>
    </transition-group>
  </el-breadcrumb>
</template>

<script type="javascript">
  export default {
    created() {
      this.getBreadcrumb()
    },
    data() {
      return {
        levelList: null
      }
    },
    watch: {
      $route() {
        this.getBreadcrumb()
      }
    },
    methods: {
      getBreadcrumb() {
        let matched = this.$route.matched.filter(item => item.name)
        const first = matched[0]
        if (first && first.name !== 'dashboard') {
          matched = [{
            path: '/dashboard',
            meta: {
              title: () => this.$t('routes.dashboard')
            }
          }].concat(matched)
        }
        this.levelList = matched
      },
      renderTitle(title){
        if (title && title.constructor === Function) {
          return title();
        }
        return title;
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../styles/variables.scss";

  .app-breadcrumb.el-breadcrumb {
    display: inline-block;
    font-size: 14px;
    line-height: 50px;
    margin-left: 10px;
    .no-redirect {
      color: $textSecondary;
      cursor: text;
    }
  }
</style>
