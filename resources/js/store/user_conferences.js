import axios from 'axios'
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
        fetchJoinedConferences({ commit }, user_id) {
            axios.get(`/api/conferences/joined/${user_id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_JOINED_CONFERENCES_ID', res.data.conferences_id)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        joinConference({ commit }, query) {
            axios.get(`/api/conferences/join/${query.user_id}/${query.conference_id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('ADD_JOINED_CONFERENCE_ID', query.conference_id)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        cancelParticipation({ commit }, query) {
            axios.get(`/api/conferences/cancel/${query.user_id}/${query.conference_id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('REMOVE_JOINED_CONFERENCE_ID', query.conference_id)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        removeJoinedConferences({ commit }) {
            commit('SET_JOINED_CONFERENCES_ID', [])
        }
    }
}
