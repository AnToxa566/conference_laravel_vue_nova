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
        SET_USER (state, value) {
            state.user = value
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
                    if (res.data.status === 'ok') {
                        commit('SET_USER', res.data.user)
                        commit('SET_AUTHENTICATED', true)

                        // store.dispatch('favorite/fetchFavoritedLecturesId')
                        // store.dispatch('user_conferences/fetchJoinedConferences')

                        dispatch('fetchUserData')

                        router.push({ name: 'conferences' })
                    }
                })
                .catch(err => {
                    commit('SET_USER', {})
                    commit('SET_AUTHENTICATED', false)

                    commit('SET_AUTH_ERRORS', err.response.data.error)
                    commit('SET_HAS_AUTH_ERRORS', true)

                    console.log(err.response.data.error)
                })
        },

        register({ commit, dispatch }, user) {
            axios.post('/api/register', user)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_USER', res.data.user)
                        commit('SET_AUTHENTICATED', true)
                        commit('SET_AUTH_ERRORS', {})

                        // store.dispatch('favorite/fetchFavoritedLecturesId')
                        // store.dispatch('user_conferences/fetchJoinedConferences')

                        dispatch('fetchUserData')

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

        update({ commit }, user) {
            axios.post('/api/profile/update', user)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_USER', res.data.user)
                        commit('SET_AUTH_ERRORS', {})

                        router.push({ name: 'conferences' })
                    }
                })
                .catch(err => {
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
