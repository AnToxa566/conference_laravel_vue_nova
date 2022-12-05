import axios from 'axios'
import store from '../store'
import router from '../router'
import { comment } from 'postcss'

export default {
    namespaced: true,

    state: {
        commentsOfLecture: [],
    },

    getters: {
        commentsOfLecture(state) {
            return state.commentsOfLecture
        },
    },

    mutations: {
        SET_COMMENTS_OF_LECTURE (state, value) {
            state.commentsOfLecture = value
        },
        PUSH_COMMENT (state, value) {
            state.commentsOfLecture.push(value)
        },
    },

    actions: {
        fetchCommentsOfLecture({ commit }, lecture_id) {
            axios.get(`/api/comments/${lecture_id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_COMMENTS_OF_LECTURE', res.data.comments)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        storeComment({ commit }, comment) {
            axios.post('/api/comments/add', comment)
                .then(res => {
                    if (res.data.status === 'ok') {
                        console.log(res.data.comment)
                        commit('PUSH_COMMENT', res.data.comment)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
