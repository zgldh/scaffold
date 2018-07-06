const getters = {
  sidebar: state => state.app.sidebar,
  language: state => state.app.language,
  hasPermission: (state, getters) => getters['currentUser/hasPermission']
}
export default getters
