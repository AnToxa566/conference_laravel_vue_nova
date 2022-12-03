import axios from 'axios'
import store from '../store'
import router from '../router'

export default {
    namespaced: true,

    state: {
        lecture: null,
        lectures: [],
    },

    getters: {
        lecture(state) {
            return state.lecture
        },
        lectures(state) {
            return state.lectures
        },

        isUserOwnThisLecture(state) {
            return parseInt(store.state.auth.user.id, 10) === parseInt(state.lecture.user_id, 10)
        },
    },

    mutations: {
        SET_LECTURE (state, value) {
            state.lecture = value
        },
        SET_LECTURES (state, value) {
            state.lectures = value
        },

        ADD_LECTURE (state, value) {
            state.lectures.push(value)
        },
        DELETE_LECTURE (state, id) {
            let index = state.lectures.map(lecture => lecture.id).indexOf(id);
            state.lectures.splice(index, 1);
        },
    },

    actions: {
        fetchAllLectures({ commit }) {
            axios.get('/api/lectures')
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_LECTURES', res.data.lectures)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        fetchLectureById({ commit }, id) {
            axios.get(`/api/lectures/${id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_LECTURE', res.data.lecture)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        storeLecture({ commit }, lecture) {
            axios.post('/api/lectures/add', lecture)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('ADD_LECTURE', res.data.lecture)
                        //router.push({ name: 'conferences' }) // TODO: detail lecture
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        deleteLecture({ commit }, query) {
            let user_id = query.user_id
            let conference_id = query.conference_id

            axios.get(`/api/lectures/delete/${user_id}/${conference_id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('DELETE_LECTURE', res.data.lecture_id)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
