import axios from 'axios'
import router from '../router'

export default {
    namespaced: true,

    state: {
        authenticated: false,
        user: {}
    },

    getters: {
        authenticated(state) {
            return state.authenticated
        },
        user(state) {
            return state.user
        }
    },

    mutations: {
        SET_AUTHENTICATED (state, value) {
            state.authenticated = value
        },
        SET_USER (state, value) {
            state.user = value
        }
    },

    actions: {
        login({ commit }, user) {
            axios.post('/api/login', user)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_USER', res.data.user)
                        commit('SET_AUTHENTICATED', true)
                        router.push({ name: 'conferences' })
                    }
                })
                .catch(err => {
                    commit('SET_USER', {})
                    commit('SET_AUTHENTICATED', false)
                    console.log(err.response)
                })
        },

        register({ commit }, user) {
            axios.post('/api/register', user)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_USER', res.data.user)
                        commit('SET_AUTHENTICATED', true)
                        router.push({ name: 'conferences' })
                    }
                })
                .catch(err => {
                    commit('SET_USER', {})
                    commit('SET_AUTHENTICATED', false)
                    console.log(err.response)
                })
        },

        logout({ commit }) {
            axios.get('/api/logout')
                .then(res => {
                    if (res.data === 'ok') {
                        commit('SET_USER', {})
                        commit('SET_AUTHENTICATED', false)
                        router.push({ name: 'conferences' })
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        }
    }
}
