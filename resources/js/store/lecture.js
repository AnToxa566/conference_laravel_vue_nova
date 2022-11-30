import axios from 'axios'
import router from '../router'

export default {
    namespaced: true,

    state: {
        lecture: null,
        lectures: []
    },

    getters: {
        lecture(state) {
            return state.lecture
        },
        lectures(state) {
            return state.lectures
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
    },

    actions: {
        storeLecture({ commit }, lecture) {
            axios.post('/api/lectures/add', lecture)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('ADD_LECTURE', res.data.lecture)
                        router.push({ name: 'conferences' })
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
