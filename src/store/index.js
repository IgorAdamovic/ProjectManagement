import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

export default new Vuex.Store({
  plugins: [createPersistedState()],
  state: {
    accessToken: null,
  },
  mutations: {
    createAccessToken(state, payload) {
      state.accessToken = payload
    },
  },
  actions: {
    signUserIn({ commit }, payload) {
      commit('createAccessToken', {
        id: payload.idUsers,
        uidUsers: payload.uidUsers,
        password: payload.pwdUsers,
        role: payload.role,
      })
    },
    logout({ commit }) {
      commit('createAccessToken', null)
    },
  },
  getters: {
    accessToken(state) {
      return state.accessToken
    },
  },
})
