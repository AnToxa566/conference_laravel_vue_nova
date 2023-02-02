import axios from 'axios'
import store from '../store'
import router from '../router'
import userTypes from '../config/user_types'
import pagination from '../config/pagination'

export default {
    namespaced: true,

    state: {
        user: {},
        authenticated: false,

        userTypes: [],

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
        SET_CONFIG (state, authToken) {
            state.config = {
                headers: {
                    Authorization: `Bearer ${authToken}`,
                    Accept :'application/json',
                }
            }

            localStorage.setItem('config', JSON.stringify(state.config))
            localStorage.setItem('authToken', JSON.stringify(authToken))
        },
        SET_AUTHENTICATED (state, value) {
            state.authenticated = value
        },

        SET_USER_TYPES (state, value) {
            state.userTypes = value
        },

        SET_AUTH_ERRORS (state, value) {
            state.authErrors = value
        },
        SET_HAS_AUTH_ERRORS (state, value) {
            state.hasAuthErrors = value
        },

        DECREMENT_JOINS (state) {
            state.user.joins_left--
        },
    },

    actions: {
        initData({ commit }) {
            commit('SET_USER_TYPES', [ userTypes.LISTENER, userTypes.ANNOUNCER ])
        },

        fetchUserData() {
            store.dispatch('favorite/fetchFavoritedLecturesId')
            store.dispatch('user_conferences/fetchJoinedConferences')
        },

        checkAuth({ state }) {
            axios.get('/api/auth/check', state.config)
                .then(res => {
                    //
                })
                .catch(err => {
                    if (state.authenticated && err.response.status === 401) {
                        commit('SET_USER', {})
                        commit('SET_AUTHENTICATED', false)

                        store.dispatch('conference/fetchPaginatedConferences', {
                            page: 1,
                            perPage: pagination.PER_PAGE,
                        })
                        store.commit('user_conferences/SET_JOINED_CONFERENCES_ID', [])

                        router.push({ name: 'login' })
                    }
                })
        },

        fetchUser({ commit, state }) {
            axios.get('/api/user', state.config)
                .then(res => {
                    commit('SET_USER', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        login({ commit, dispatch }, user) {
            axios.post('/api/login', user)
                .then(res => {
                    if (res.data.type !== userTypes.ADMIN) {
                        commit('SET_USER', res.data)
                        commit('SET_CONFIG', res.data)
                        commit('SET_AUTHENTICATED', true)

                        dispatch('fetchUserData')

                        router.push({ name: 'conferences' })
                    }
                    else {
                        dispatch('loginToNova', user)
                    }
                })
                .catch(err => {
                    commit('SET_USER', {})
                    commit('SET_AUTHENTICATED', false)

                    commit('SET_AUTH_ERRORS', err.response.data.message)
                    commit('SET_HAS_AUTH_ERRORS', true)

                    console.log(err.response)
                })
        },

        loginToNova({ }, user) {
            axios.post('/nova/login', user)
                .then(res => {
                    window.location.href = '/nova'
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        register({ commit, dispatch }, user) {
            axios.post('/api/register', user)
                .then(res => {
                    commit('SET_USER', res.data.user)
                    commit('SET_CONFIG', res.data.auth_token)

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

                    store.dispatch('conference/fetchPaginatedConferences', {
                        page: 1,
                        perPage: pagination.PER_PAGE,
                    })
                    store.commit('user_conferences/SET_JOINED_CONFERENCES_ID', [])

                    localStorage.removeItem('config')
                    localStorage.removeItem('authToken')

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
