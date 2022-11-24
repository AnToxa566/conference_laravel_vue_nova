import axios from 'axios'
import router from '../router'
import moment from 'moment'

export default {
    namespaced: true,

    state: {
        conferences: [],
        countries: [],
        conference: null,
        conferencesPaginatedDate: null,
    },

    getters: {
        conferences(state) {
            return state.conferences
        },
        countriesName(state) {
            return state.countries.map(country => country.name)
        },
        conference(state) {
            return state.conference
        },
        conferencesPaginatedDate(state) {
            return state.conferencesPaginatedDate
        },
        formatedDateTime(state) {
            return id => {
                const conferencesId = state.conferences.map(conference => conference.id);
                const index = conferencesId.indexOf(parseInt(id, 10));
                const conference = state.conferences[index];

                return moment(String(conference.date_time_event)).format('MMMM Do YYYY, h:mm a')
            }
        }
    },

    mutations: {
        SET_CONFERENCES (state, value) {
            state.conferences = value
        },
        SET_COUNTRIES (state, value) {
            state.countries = value
        },
        SET_CONFERENCE (state, value) {
            state.conference = value
        },
        SET_CONFERENCES_PAGINATED_DATE (state, value) {
            state.conferencesPaginatedDate = value
        },
        ADD_CONFERENCE (state, value) {
            state.conferences.push(value)
        },
        UPDATE_CONFERENCE (state, value) {
            const index = state.conferences.map(conference => conference.id).indexOf(value.id);
            state.conferences.splice(index, 1, value);
        },
        DELETE_CONFERENCE (state, id) {
            const index = state.conferences.map(conference => conference.id).indexOf(id);
            state.conferences.splice(index, 1);
        },
    },

    actions: {
        fetchAllConferences({ commit }, page) {
            axios.get('/api/conferences')
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_CONFERENCES', res.data.conferences)
                        commit('SET_COUNTRIES', res.data.countries)

                        const pagination = {
                            current_page: page,
                        }

                        commit('SET_CONFERENCES_PAGINATED_DATE', pagination)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        fetchDetailConference({ commit }, id) {
            axios.get(`/api/conferences/${id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_CONFERENCE', res.data.conference)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        storeConference({ commit }, conference) {
            axios.post('/api/conferences/add', conference)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('ADD_CONFERENCE', res.data.conference)
                        router.push({ name: 'conferences' })
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        updateConference({ commit }, conference) {
            axios.post(`/api/conferences/${conference.id}/update`, conference)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('UPDATE_CONFERENCE', res.data.conference)
                        router.push(`/conferences/${res.data.conference.id}`)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        deleteConference({ commit }, id) {
            axios.get(`/api/conferences/${id}/delete`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('DELETE_CONFERENCE', id)
                        router.push({ name: 'conferences' })
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
