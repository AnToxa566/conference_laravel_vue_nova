import axios from 'axios'
import store from '../store'
import router from '../router'
import userTypes from '../config/user_types'

export default {
    namespaced: true,

    state: {
        user: {},
        authenticated: false,

        userTypes: [],
        adminType: '',

        authErrors: {},
        hasAuthErrors: false,

        config: null,
    },

    getters: {
        user(state) {
            return state.user
        },
        authenticated(state) {
            return state.authenticated
        },

        userTypes(state) {
            return state.userTypes
        },
        adminType(state) {
            return state.adminType
        },
        isAdmin(state) {
            return state.user.type === state.adminType
        },

        authErrors(state) {
            return state.authErrors
        },
        hasAuthErrors(state) {
            return state.hasAuthErrors
        },
    },

    mutations: {
        SET_USER (state, user) {
            state.user = user
        },
        SET_CONFIG (state, user) {
            state.config = {
                headers: {
                    Authorization: `Bearer ${user.auth_token}`,
                    Accept :'application/json',
                }
            }

            localStorage.setItem('config', JSON.stringify(state.config))
        },
        SET_AUTHENTICATED (state, value) {
            state.authenticated = value
        },

        SET_USER_TYPES (state, value) {
            state.userTypes = value
        },
        SET_ADMIN_TYPE (state, value) {
            state.adminType = value
        },

        SET_AUTH_ERRORS (state, value) {
            state.authErrors = value
        },
        SET_HAS_AUTH_ERRORS (state, value) {
            state.hasAuthErrors = value
        },
    },

    actions: {
        initData({ commit }) {
            commit('SET_USER_TYPES', [ userTypes.LISTENER, userTypes.ANNOUNCER ])
            commit('SET_ADMIN_TYPE', userTypes.ADMIN)
        },

        fetchUserData() {
            store.dispatch('favorite/fetchFavoritedLecturesId')
            store.dispatch('user_conferences/fetchJoinedConferences')
        },

        login({ commit, dispatch }, user) {
            axios.post('/api/login', user)
                .then(res => {
                    commit('SET_USER', res.data)
                    commit('SET_CONFIG', res.data)
                    commit('SET_AUTHENTICATED', true)

                    dispatch('fetchUserData')

                    router.push({ name: 'conferences' })
                })
                .catch(err => {
                    commit('SET_USER', {})
                    commit('SET_AUTHENTICATED', false)

                    commit('SET_AUTH_ERRORS', err.response.data.message)
                    commit('SET_HAS_AUTH_ERRORS', true)

                    console.log(err.response)
                })
        },

        register({ commit, dispatch }, user) {
            axios.post('/api/register', user)
                .then(res => {
                    commit('SET_USER', res.data)
                    commit('SET_CONFIG', res.data)

                    commit('SET_AUTHENTICATED', true)
                    commit('SET_AUTH_ERRORS', {})

                    dispatch('fetchUserData')

                    router.push({ name: 'conferences' })
                })
                .catch(err => {
                    commit('SET_USER', {})
                    commit('SET_AUTHENTICATED', false)

                    commit('SET_AUTH_ERRORS', err.response.data.message)
                    commit('SET_HAS_AUTH_ERRORS', true)

                    console.log(err.response)
                })
        },

        update({ commit, state }, user) {
            axios.post('/api/profile/update', user, state.config)
                .then(res => {
                    commit('SET_USER', res.data)
                    commit('SET_AUTH_ERRORS', {})

                    router.push({ name: 'conferences' })
                })
                .catch(err => {
                    commit('SET_AUTH_ERRORS', err.response.data.message)
                    commit('SET_HAS_AUTH_ERRORS', true)

                    console.log(err.response)
                })
        },

        logout({ commit, state }) {
            axios.get('/api/logout', state.config)
                .then(res => {
                    commit('SET_USER', {})
                    commit('SET_AUTHENTICATED', false)

                    store.dispatch('user_conferences/removeJoinedConferences')

                    localStorage.removeItem('config')

                    router.push({ name: 'conferences' })
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
