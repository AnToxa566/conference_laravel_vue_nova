import axios from 'axios'

export default {
    namespaced: true,

    state: {
        meetings: [],
        apiMeetings: [],
    },

    getters: {
        meetings(state) {
            return state.meetings
        },

        apiMeetings(state) {
            return state.apiMeetings
        },

        meetingsOfExistingLectures(state) {
            const meetingsIds = state.meetings.map(m => m.id)
            const meetings = state.apiMeetings.filter(m => meetingsIds.includes(m.id))

            return meetings
        },
    },

    mutations: {
        SET_MEETINGS (state, meetings) {
            state.meetings = meetings
        },

        SET_API_MEETINGS (state, meetings) {
            state.apiMeetings = meetings
        },

        PUSH_MEETING (state, meeting) {
            state.meetings.push(meeting)
        },
    },

    actions: {
        fetchMeetingsFromAPI({ commit }) {
            axios.get(`/api/meetings/api`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('SET_API_MEETINGS', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        fetchMeetingsFromDB({ commit }) {
            axios.get(`/api/meetings/db`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('SET_MEETINGS', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
