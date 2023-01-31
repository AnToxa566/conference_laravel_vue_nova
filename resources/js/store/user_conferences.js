import axios from 'axios'
import store from '../store'
import router from '../router'

export default {
    namespaced: true,

    state: {
        joinedConferencesId: []
    },

    getters: {
        joinedConferencesId(state) {
            return state.joinedConferencesId
        },
    },

    mutations: {
        SET_JOINED_CONFERENCES_ID (state, value) {
            state.joinedConferencesId = value
        },
        ADD_JOINED_CONFERENCE_ID (state, value) {
            state.joinedConferencesId.push(parseInt(value, 10))
        },
        REMOVE_JOINED_CONFERENCE_ID (state, value) {
            const index = state.joinedConferencesId.indexOf(parseInt(value, 10));
            state.joinedConferencesId.splice(index, 1);
        },
    },

    actions: {
        fetchJoinedConferences({ commit }) {
            axios.get(`/api/conferences/joined/${store.state.auth.user.id}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('SET_JOINED_CONFERENCES_ID', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        joinConference({ commit }, conferenceId) {
            axios.post(`/api/conferences/join`, { conferenceId: conferenceId }, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('ADD_JOINED_CONFERENCE_ID', conferenceId)
                    store.commit('auth/DECREMENT_JOINS')
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        cancelParticipation({ commit }, conferenceId) {
            axios.post(`/api/conferences/cancel`, { conferenceId: conferenceId }, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('REMOVE_JOINED_CONFERENCE_ID', conferenceId)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
