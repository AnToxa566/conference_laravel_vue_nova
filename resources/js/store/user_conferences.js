import axios from 'axios'
import store from '../store'
import router from '../router'

export default {
    namespaced: true,

    state: {
        joinedConferencesId: []
    },

    getters: {
        joinedConferencesId: state => state.joinedConferencesId,
    },

    mutations: {
        storeJoinedConferencesId (state, value) {
            state.joinedConferencesId = value
        },

        deleteJoinedConferencesId (state, value) {
            const index = state.joinedConferencesId.indexOf(parseInt(value, 10));
            state.joinedConferencesId.splice(index, 1);
        },
    },

    actions: {
        fetchJoinedConferences({ commit }) {
            axios.get(`/api/conferences/joined/${store.state.auth.user.id}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storeJoinedConferencesId', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        fetchDataAfterJoinConference({ dispatch }) {
            dispatch('fetchJoinedConferences')
            store.dispatch('auth/fetchUser')
        },

        listenerJoinConference({ dispatch }, conferenceId) {
            axios.post(`/api/conferences/join`, { conferenceId: conferenceId }, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    dispatch('fetchDataAfterJoinConference')
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        cancelParticipation({ commit }, conferenceId) {
            axios.post(`/api/conferences/cancel`, { conferenceId: conferenceId }, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('deleteJoinedConferencesId', conferenceId)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
