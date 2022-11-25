import axios from 'axios'
import router from '../router'

export default {
    namespaced: true,

    state: {
        user: {},
        userTypes: ['Listener', 'Speaker'],
        authenticated: false,

        authErrors: {},
        hasAuthErrors: false,
    },

    getters: {
        user(state) {
            return state.user
        },
        userTypes(state) {
            return state.userTypes
        },
        authenticated(state) {
            return state.authenticated
        },
        authErrors(state) {
            return state.authErrors
        },
        hasAuthErrors(state) {
            return state.hasAuthErrors
        },
    },

    mutations: {
        SET_AUTHENTICATED (state, value) {
            state.authenticated = value
        },
        SET_USER (state, value) {
            state.user = value
        },
        SET_AUTH_ERRORS (state, value) {
            state.authErrors = value
        },
        SET_HAS_AUTH_ERRORS (state, value) {
            state.hasAuthErrors = value
        },
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

                    console.log(err.response.data.error)

                    commit('SET_AUTH_ERRORS', err.response.data.error)
                    commit('SET_HAS_AUTH_ERRORS', true)
                })
        },

        register({ commit }, user) {
            axios.post('/api/register', user)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_USER', res.data.user)
                        commit('SET_AUTHENTICATED', true)
                        commit('SET_REGISTER_ERRORS', {})
                        router.push({ name: 'conferences' })
                    }
                })
                .catch(err => {
                    commit('SET_USER', {})
                    commit('SET_AUTHENTICATED', false)

                    commit('SET_AUTH_ERRORS', err.response.data.error)
                    commit('SET_HAS_AUTH_ERRORS', true)
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
        },

        removeAuthErrors({ commit }) {
            commit('SET_AUTH_ERRORS', {})
            commit('SET_HAS_AUTH_ERRORS', false)
        },
    }
}
