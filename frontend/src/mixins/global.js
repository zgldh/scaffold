var globalMixin = {
  methods: {
    hasPermission(permission) {
      return this.$store.getters['currentUser/hasPermission'](permission)
    }
  }
}

export default globalMixin
