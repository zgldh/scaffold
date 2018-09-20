const getters = {
  sidebar: state => state.app.sidebar,
  language: state => state.app.language,
  hasPermission: (state, getters) => getters['currentUser/hasPermission'],
  visitedViews: state => state.tagsView.visitedViews,
  cachedViews: state => state.tagsView.cachedViews
}
export default getters
