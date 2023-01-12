import axios from 'axios'
import store from '../store'
import router from '../router'

export default {
    namespaced: true,

    state: {
        commentsOfLecture: [],
    },

    getters: {
        commentsOfLecture(state) {
            return state.commentsOfLecture
        },

        canBeUpdated: (state) => (id) => {
            const updated_at = state.commentsOfLecture.find(comment => comment.id == parseInt(id, 10)).updated_at;
            const passedTime = Date.now() - (new Date(updated_at))

            return (passedTime / 60000) < 10
        },
    },

    mutations: {
        SET_COMMENTS_OF_LECTURE (state, value) {
            state.commentsOfLecture = value
        },
        PUSH_COMMENT (state, value) {
            state.commentsOfLecture.unshift(value)
        },
        PUSH_COMMENTS (state, comments) {
            comments.forEach(comment => {
                state.commentsOfLecture.push(comment)
            })
        },
        UPDATE_COMMENT (state, value) {
            const index = state.commentsOfLecture.map(comment => comment.id).indexOf(value.id);
            state.commentsOfLecture.splice(index, 1, value);
        },
    },

    actions: {
        fetchMoreCommentsOfLecture({ commit }, query) {
            axios.get(`/api/comments/${query.lecture_id}/limit/${query.limit}/page/${query.page}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    if (query.page == 1) {
                        commit('SET_COMMENTS_OF_LECTURE', res.data)
                    }
                    else {
                        commit('PUSH_COMMENTS', res.data)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        storeComment({ commit }, comment) {
            axios.post('/api/comments/add', comment, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('PUSH_COMMENT', res.data)
                    store.dispatch('lecture/incrementCommentsCount', res.data.lecture_id)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        updateComment({ commit }, comment) {
            axios.post(`/api/comments/${comment.id}/update`, comment, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('UPDATE_COMMENT', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
