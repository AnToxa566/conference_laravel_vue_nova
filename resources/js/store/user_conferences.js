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
            axios.get(`/api/conferences/joined/${store.state.auth.user.id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_JOINED_CONFERENCES_ID', res.data.conferences_id)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        joinConference({ commit }, conference_id) {
            axios.get(`/api/conferences/join/${store.state.auth.user.id}/${conference_id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('ADD_JOINED_CONFERENCE_ID', conference_id)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        cancelParticipation({ commit }, conference_id) {
            axios.get(`/api/conferences/cancel/${store.state.auth.user.id}/${conference_id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('REMOVE_JOINED_CONFERENCE_ID', conference_id)
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
