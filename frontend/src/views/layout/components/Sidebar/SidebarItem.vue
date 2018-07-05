<template>
  <div class="menu-wrapper">
    <template v-for="item in visibleRoutes" v-if="!item.hidden&&item.children">

      <router-link
              v-if="item.children.length===1 && !item.children[0].children && !item.alwaysShow"
              :to="item.path+'/'+item.children[0].path" :key="item.children[0].name">
        <el-menu-item :index="item.path+'/'+item.children[0].path"
                      :class="{'submenu-title-noDropdown':!isNest}">
          <auto-icon v-if="item.children[0].meta&&item.children[0].meta.icon"
                     :icon-class="item.children[0].meta.icon"></auto-icon>
          <span v-if="item.children[0].meta&&item.children[0].meta.title">{{renderTitle(item.children[0].meta.title)}}</span>
        </el-menu-item>
      </router-link>

      <el-submenu v-else :index="item.name||item.path" :key="item.name">
        <template slot="title">
          <auto-icon v-if="item.meta&&item.meta.icon"
                     :icon-class="item.meta.icon"></auto-icon>
          <span v-if="item.meta&&item.meta.title">{{renderTitle(item.meta.title)}}</span>
        </template>

        <template v-for="child in item.children" v-if="!child.hidden">
          <sidebar-item :is-nest="true" class="nest-menu"
                        v-if="child.children&&child.children.length>0" :routes="[child]"
                        :key="child.path"></sidebar-item>

          <router-link v-else :to="item.path+'/'+child.path" :key="child.name">
            <el-menu-item :index="item.path+'/'+child.path">
              <auto-icon v-if="child.meta&&child.meta.icon"
                         :icon-class="child.meta.icon"></auto-icon>
              <span v-if="child.meta&&child.meta.title">{{renderTitle(child.meta.title)}}</span>
            </el-menu-item>
          </router-link>
        </template>
      </el-submenu>

    </template>
  </div>
</template>

<script type="javascript">
  function getVisibleChild(item) {
    if (item.children) {
      item.children = item.children.filter(child => child.hidden !== true)
      item.children = item.children.map(getVisibleChild)
    }
    return item
  }

  export default {
    name: 'SidebarItem',
    props: {
      routes: {
        type: Array
      },
      isNest: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      visibleRoutes(){
        var visibleRoutes = this.routes.filter(route => route.hidden !== true)
        visibleRoutes = visibleRoutes.map(getVisibleChild)
        return visibleRoutes
      }
    },
    methods: {
      renderTitle(title){
        if (title && title.constructor === Function) {
          return title();
        }
        return title;
      }
    }
  }


</script>
