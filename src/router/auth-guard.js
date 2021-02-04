import store from '../store'

export default (to, from, next) => {
  if (store.getters.accessToken) {
    next()
  } else {
    next('/login')
  }
}
