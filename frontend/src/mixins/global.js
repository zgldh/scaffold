var globalMixin = {
  methods: {
    hasPermission(permission) {
      return this.$store.getters['currentUser/hasPermission'](permission)
    }
  },
  beforeRouteLeave(to, from, next) {
    if (!this.$store.state.tagsView.visitedViews.find(item => item.path === from.path)) {
      this.$destroy()
    }
    next()
  }
}

export default globalMixin
