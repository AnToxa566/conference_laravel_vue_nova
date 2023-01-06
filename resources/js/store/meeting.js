import axios from 'axios'
import store from '../store'

export default {
    namespaced: true,

    state: {
        meetings: []
    },

    getters: {
        meetings(state) {
            return state.meetings
        },
    },

    mutations: {
        SET_MEETINGS (state, meetings) {
            state.meetings = meetings
        },

        PUSH_MEETING (state, meeting) {
            state.meetings.push(meeting)
        },
    },

    actions: {
        fetchAllMeetings({ commit }) {
            axios.get(`/api/meetings`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('SET_MEETINGS', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
