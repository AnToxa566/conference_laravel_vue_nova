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
        conference(state) {
            return state.conference
        },
        conferenceById: (state) => (id) => {
            return state.conferences.find(conference => conference.id === parseInt(id, 10));
        },

        conferences(state) {
            return state.conferences
        },
        filteredConferences(state) {
            return state.filteredConferences
        },
        searchedConferences(state) {
            return state.searchedConferences
        },
        conferencesPaginatedData(state) {
            return state.conferencesPaginatedData
        },

        error(state) {
            return state.error
        },

        formatedDateTime(state) {
            return id => {
                const conferencesId = state.conferences.map(conference => conference.id);
                const index = conferencesId.indexOf(parseInt(id, 10));
                const conference = state.conferences[index];

                if (index !== -1) {
                    return moment(String(conference.date_time_event)).format('MMMM Do YYYY, h:mm a')
                }
            }
        },

        getMinCountLectures(state) {
            const counts = state.conferences.map(conference => conference.lectures_count)
            return Math.min(...counts)
        },
        getMaxCountLectures(state) {
            const counts = state.conferences.map(conference => conference.lectures_count)
            return Math.max(...counts)
        },

        getMinDateEvent(state) {
            const dates = state.conferences.map(conference => new Date(conference.date_time_event))
            return Math.min(...dates)
        },
        getMaxDateEvent(state) {
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

        ADD_CONFERENCE (state, value) {
            state.conferences.push(value)
        },
        UPDATE_CONFERENCE (state, value) {
            const index = state.conferences.map(conference => conference.id).indexOf(value.id);
            state.conferences.splice(index, 1, value);
        },
        DELETE_CONFERENCE (state, id) {
            let index = state.conferences.map(conference => conference.id).indexOf(id);
            state.conferences.splice(index, 1);

            index = state.conferencesPaginatedData.paginatedConferences.map(conference => conference.id).indexOf(id);
            state.conferencesPaginatedData.paginatedConferences.splice(index, 1);
        },

        UPDATE_LECTURES_CATEGORIES (state, lectures) {
            lectures.forEach(lecture => {
                lecture.category_id = null
                store.dispatch('lecture/updateLecture', lecture)
            })
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


        fetchPaginatedConferences({ commit }, query) {
            const pagination = {
                currentPage: query.page,
                perPage: query.perPage,
                totalConferences: query.conferences.length,
                totalPages: Math.ceil(query.conferences.length / query.perPage),

                paginatedConferences: query.conferences.slice((query.page - 1) * query.perPage, query.page * query.perPage)
            }

            commit('SET_CONFERENCES_PAGINATED_DATA', pagination)
        },


        fetchFilteredConferences({ commit, state, dispatch }, filter) {
            axios.post('/api/conferences/filtered', filter, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('SET_FILTERED_CONFERENCES', res.data)

                    dispatch('fetchPaginatedConferences', {
                        page: 1,
                        perPage: state.conferencesPaginatedData.perPage,
                        conferences: res.data,
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


        storeConference({ commit }, conference) {
            axios.post('/api/conferences/add', conference, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('ADD_CONFERENCE', res.data)
                    commit('SET_ERROR', '')
                    router.push({ name: 'conferenceShow', params: { id: res.data.id } })
                })
                .catch(err => {
                    console.log(err.response)
                    commit('SET_ERROR', err.response.data.message)
                })
        },


        updateConference({ commit }, conference) {
            axios.post(`/api/conferences/${conference.id}/update`, conference, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('UPDATE_CONFERENCE', res.data)

                    if (res.data.lectures.length) {
                        commit('UPDATE_LECTURES_CATEGORIES', res.data.lectures)
                    }

                    commit('SET_ERROR', '')
                    router.go(-1)
                })
                .catch(err => {
                    console.log(err.response)
                    commit('SET_ERROR', err.response.data.message)
                })
        },


        updateConferenceCategories({ state, dispatch }, categories) {
            categories.forEach(category => {
                let conferences = state.conferences.filter(conf => conf.category_id === category.id)

                conferences.forEach(conference => {
                    conference.category_id = null
                    dispatch('updateConference', conference)
                })
            })
        },


        deleteConference({ commit }, id) {
            axios.get(`/api/conferences/${id}/delete`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    if (res.data) {
                        commit('DELETE_CONFERENCE', id)
                        router.push({ name: 'conferences' })
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },


        exportConferences({ }) {
            axios.get(`/api/conferences/export/all`, { ...JSON.parse(localStorage.getItem('config')), ...{ responseType: 'blob' }})
                .then(res => {
                    //
                })
                .catch(err => {
                    console.log(err)
                })
        },


        exportListeners({ }, conferenceId) {
            axios.get(`/api/conferences/export/listeners/${conferenceId}`, { ...JSON.parse(localStorage.getItem('config')), ...{ responseType: 'blob' }})
                .then(res => {
                    //
                })
                .catch(err => {
                    console.log(err)
                })
        },
    }
}
