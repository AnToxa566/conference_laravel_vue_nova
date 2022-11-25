import axios from 'axios'
import router from '../router'
import moment from 'moment'

export default {
    namespaced: true,

    state: {
        conference: null,
        conferences: [],
        conferencesPaginatedDate: null,

        countries: [],
        countriesName: [],

        addressPosition: null,
        formatedAddress: null,
    },

    getters: {
        conferences(state) {
            return state.conferences
        },
        countries(state) {
            return state.countries
        },
        countriesName(state) {
            return state.countriesName
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
        },

        addressPosition(state) {
            return state.addressPosition
        },
        formatedAddress(state) {
            return state.formatedAddress
        },
    },

    mutations: {
        SET_CONFERENCES (state, value) {
            state.conferences = value
        },
        SET_COUNTRIES (state, value) {
            state.countries = value
        },
        SET_COUNTRIES_NAME (state, value) {
            state.countriesName = value
        },
        SET_CONFERENCE (state, value) {
            state.conference = value
        },
        SET_CONFERENCES_PAGINATED_DATE (state, value) {
            state.conferencesPaginatedDate = value
        },
        SET_ADDRESS_POSITION (state, value) {
            state.addressPosition = value
        },
        SET_FORMATED_ADDRESS (state, value) {
            state.formatedAddress = value
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
        fetchAllConferences({ commit }) {
            axios.get('/api/conferences')
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_CONFERENCES', res.data.conferences)
                        commit('SET_COUNTRIES', res.data.countries)
                        commit('SET_COUNTRIES_NAME', res.data.countries.map(country => country.name))
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        fetchPaginatedConferences({ commit, state }, page) {
            const pagination = {
                current_page: page,
                per_page: 15,
                total_conferences: state.conferences.length,
                total_pages: Math.ceil(state.conferences.length / 15),

                paginated_conferences: state.conferences.slice((page - 1) * 15, page * 15)
            }

            commit('SET_CONFERENCES_PAGINATED_DATE', pagination)
        },

        fetchDetailConference({ commit, dispatch }, id) {
            axios.get(`/api/conferences/${id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_CONFERENCE', res.data.conference)
                        commit('SET_ADDRESS_POSITION', {
                            'lat': res.data.conference.latitude,
                            'lng': res.data.conference.longitude
                        })

                        dispatch('getFormatedAddress', res.data.conference)
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
                        router.push({ name: 'conferenceShow', params: { id: res.data.conference.id } })
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
                        router.go(-1)
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

        async getFormatedAddress({commit}, conference) {
            if (conference.latitude !== null && conference.longitude !== null) {
                const url = new URL('https://maps.googleapis.com/maps/api/geocode/json?latlng=' +
                            conference.latitude + ',' + conference.longitude +
                            '&key=' + import.meta.env.VITE_PUSHER_GOOGLE_MAPS_API_KEY)

                const response = await fetch(url)
                const json = await response.json()
                const address = await json.results[0].formatted_address

                commit('SET_FORMATED_ADDRESS', address)
            }
            else {
                commit('SET_FORMATED_ADDRESS', 'undefined')
            }
        },
    }
}
