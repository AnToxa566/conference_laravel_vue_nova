import axios from 'axios'

export default {
    namespaced: true,

    state: {
        meetings: [],
        apiMeetings: [],
    },

    getters: {
        meetings: state => state.meetings,

        apiMeetings: state => state.apiMeetings,

        meetingsOfExistingLectures: state => {
            const meetingsIds = state.meetings.map(m => m.id)
            const meetings = state.apiMeetings.filter(m => meetingsIds.includes(m.id))

            return meetings
        },
    },

    mutations: {
        storeMeetings (state, meetings) {
            state.meetings = meetings
        },

        storeApiMeetings (state, meetings) {
            state.apiMeetings = meetings
        },

        storeMeeting (state, meeting) {
            state.meetings.push(meeting)
        },
    },

    actions: {
        fetchMeetingsFromAPI({ commit }) {
            axios.get(`/api/meetings/api`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storeApiMeetings', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        fetchMeetingsFromDB({ commit }) {
            axios.get(`/api/meetings/db`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storeMeetings', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
