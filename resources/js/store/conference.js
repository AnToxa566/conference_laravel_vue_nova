import axios from 'axios'
import store from '../store'
import router from '../router'
import moment from 'moment'

export default {
    namespaced: true,

    state: {
        conference: {},

        conferences: [],
        filteredConferences: [],
        searchedConferences: [],
        conferencesPaginatedData: {},

        error: '',
    },

    getters: {
        conference: state => state.conference,

        conferenceById: state => id => state.conferences.find(conf => conf.id == id),

        conferences: state => state.conferences,

        filteredConferences: state => state.filteredConferences,

        searchedConferences: state => state.searchedConferences,

        conferencesPaginatedData: state => state.conferencesPaginatedData,

        error: state => state.error,

        formatedDateTime: state => id => {
            const conferencesId = state.conferences.map(conference => conference.id);
            const index = conferencesId.indexOf(parseInt(id, 10));
            const conference = state.conferences[index];

            if (index !== -1) {
                return moment(String(conference.date_time_event)).format('MMMM Do YYYY, h:mm a')
            }
        },

        getMinCountLectures: state => {
            const counts = state.conferences.map(conference => conference.lectures_count)
            return Math.min(...counts)
        },

        getMaxCountLectures: state => {
            const counts = state.conferences.map(conference => conference.lectures_count)
            return Math.max(...counts)
        },

        getMinDateEvent: state => {
            const dates = state.conferences.map(conference => new Date(conference.date_time_event))
            return Math.min(...dates)
        },

        getMaxDateEvent: state => {
            const dates = state.conferences.map(conference => new Date(conference.date_time_event))
            return Math.max(...dates)
        },
    },

    mutations: {
        SET_CONFERENCE (state, value) {
            state.conference = value
        },
        SET_CONFERENCES (state, value) {
            state.conferences = value
        },
        SET_FILTERED_CONFERENCES (state, value) {
            state.filteredConferences = value
        },
        SET_SEARCHED_CONFERENCES (state, value) {
            state.searchedConferences = value
        },
        SET_CONFERENCES_PAGINATED_DATA (state, value) {
            state.conferencesPaginatedData = value
        },

        SET_ERROR (state, value) {
            state.error = value
        },

        LECTURE_COUNT_INCREMENT (state, id) {
            const index = state.conferences.findIndex(c => c.id == parseInt(id, 10));
            state.conferences[index].lectures_count++
        },

        LECTURE_COUNT_DECREMENT (state, id) {
            const index = state.conferences.findIndex(c => c.id == parseInt(id, 10));
            state.conferences[index].lectures_count--
        },
    },

    actions: {
        fetchAllConferences({ commit }) {
            axios.get('/api/conferences')
                .then(res => {
                    commit('SET_ERROR', '')
                    commit('SET_CONFERENCES', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },


        fetchPaginatedConferences({ commit, state }, query) {
            const conferences = store.state.auth.authenticated ? state.filteredConferences : state.conferences

            const pagination = {
                currentPage: query.page,
                perPage: query.perPage,
                totalConferences: conferences.length,
                totalPages: Math.ceil(conferences.length / query.perPage),

                paginatedConferences: conferences.slice((query.page - 1) * query.perPage, query.page * query.perPage)
            }

            commit('SET_CONFERENCES_PAGINATED_DATA', pagination)
        },


        fetchFilteredConferences({ commit, state, dispatch }, filter) {
            axios.post('/api/conferences/filtered', filter, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('SET_FILTERED_CONFERENCES', res.data)

                    dispatch('fetchPaginatedConferences', {
                        page: state.conferencesPaginatedData.currentPage,
                        perPage: state.conferencesPaginatedData.perPage,
                    })

                    commit('SET_ERROR', '')
                })
                .catch(err => {
                    console.log(err.response)
                    commit('SET_ERROR', err.response.data.message)
                })
        },


        fetchDetailConference({ commit, dispatch }, id) {
            axios.get(`/api/conferences/${id}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('SET_CONFERENCE', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },


        searchConferences({ commit }, query) {
            axios.get(`/api/conferences/search/${query.search}/limit/${query.limit}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('SET_SEARCHED_CONFERENCES', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
