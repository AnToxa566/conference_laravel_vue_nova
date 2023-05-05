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

        config: {},
    },

    getters: {
        user: state => state.user,

        authenticated: state => state.authenticated,

        userTypes: state => state.userTypes,

        authErrors: state => state.authErrors,

        hasAuthErrors: state => state.hasAuthErrors,
    },

    mutations: {
        storeUser (state, user) {
            state.user = user
        },

        storeConfig (state, authToken) {
            state.config = {
                headers: {
                    Authorization: `Bearer ${authToken}`,
                    Accept :'application/json',
                }
            }

            localStorage.setItem('config', JSON.stringify(state.config))
            localStorage.setItem('authToken', JSON.stringify(authToken))
        },

        storeAuthenticated (state, value) {
            state.authenticated = value
        },

        storeUserTypes (state, value) {
            state.userTypes = value
        },

        storeAuthErrors (state, value) {
            state.authErrors = value
        },

        storeHasAuthErrors (state, value) {
            state.hasAuthErrors = value
        },
    },

    actions: {
        initData({ commit }) {
            commit('storeUserTypes', [ userTypes.LISTENER, userTypes.ANNOUNCER ])
        },

        fetchUserData() {
            store.dispatch('favorite/fetchFavoritedLecturesId')
            store.dispatch('user_conferences/fetchJoinedConferences')
        },

        checkAuth({ state, commit }) {
            axios.get('/api/auth/check', state.config)
                .then(res => {
                    //
                })
                .catch(err => {
                    if (state.authenticated && err.response.status === 401) {
                        commit('storeUser', {})
                        commit('storeAuthenticated', false)

                        store.dispatch('conference/fetchPaginatedConferences', {
                            page: 1,
                            perPage: pagination.PER_PAGE,
                        })
                        store.commit('user_conferences/storeJoinedConferencesId', [])

                        router.push({ name: 'login' })
                    }
                })
        },

        fetchUser({ commit, state }) {
            axios.get('/api/user', state.config)
                .then(res => {
                    commit('storeUser', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        login({ commit, dispatch }, user) {
            axios.post('/api/login', user)
                .then(res => {
                    commit('storeUser', res.data.user)
                    commit('storeConfig', res.data.auth_token)
                    commit('storeAuthenticated', true)

                    dispatch('fetchUserData')

                    router.push({ name: 'conferences' })
                })
                .catch(err => {
                    commit('storeUser', {})
                    commit('storeAuthenticated', false)

                    commit('storeAuthErrors', err.response.data.message)
                    commit('storeHasAuthErrors', true)

                    console.log(err.response)
                })
        },

        register({ commit, dispatch }, user) {
            axios.post('/api/register', user)
                .then(res => {
                    commit('storeUser', res.data.user)
                    commit('storeConfig', res.data.auth_token)

                    commit('storeAuthenticated', true)
                    commit('storeAuthErrors', {})

                    dispatch('fetchUserData')

                    router.push({ name: 'conferences' })
                })
                .catch(err => {
                    commit('storeUser', {})
                    commit('storeAuthenticated', false)

                    commit('storeAuthErrors', err.response.data.message)
                    commit('storeHasAuthErrors', true)

                    console.log(err.response)
                })
        },

        update({ commit, state }, user) {
            axios.post('/api/profile/update', user, state.config)
                .then(res => {
                    commit('storeUser', res.data)
                    commit('storeAuthErrors', {})

                    router.push({ name: 'conferences' })
                })
                .catch(err => {
                    commit('storeAuthErrors', err.response.data.message)
                    commit('storeHasAuthErrors', true)

                    console.log(err.response)
                })
        },

        logout({ commit, state }) {
            axios.get('/api/logout', state.config)
                .then(res => {
                    commit('storeUser', {})
                    commit('storeAuthenticated', false)

                    store.dispatch('conference/fetchPaginatedConferences', {
                        page: 1,
                        perPage: pagination.PER_PAGE,
                    })
                    store.commit('user_conferences/storeJoinedConferencesId', [])

                    localStorage.removeItem('config')
                    localStorage.removeItem('authToken')

                    router.push({ name: 'conferences' })
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        removeAuthErrors({ commit }) {
            commit('storeAuthErrors', {})
            commit('storeHasAuthErrors', false)
        },
    }
}
