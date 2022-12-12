import axios from 'axios'
import store from '../store'

export default {
    namespaced: true,

    state: {
        categories: [],
        subcategories: [],
    },

    getters: {
        categories(state) {
            return state.categories
        },
        subcategories(state) {
            return state.subcategories
        },

        getRoots(state) {
            return state.categories.filter(category => !category.parent_id).map(category => category.id)
        },
    },

    mutations: {
        SET_CATEGORIES (state, value) {
            state.categories = value
        },
        SET_SUBCATEGORIES (state, value) {
            state.subcategories = value
        },
    },

    actions: {
        fetchAllCategories({ commit }) {
            axios.get('/api/category')
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_CATEGORIES', res.data.categories)
                        commit('SET_SUBCATEGORIES', res.data.subcategories)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        // addLectureToFavorite({ commit }, lectureId) {
        //     axios.get(`/api/lectures/favorite/add/${store.state.auth.user.id}/${lectureId}`)
        //         .then(res => {
        //             if (res.data.status === 'ok') {
        //                 commit('PUSH_FAVORITE_LECTURE_ID', lectureId)
        //             }
        //         })
        //         .catch(err => {
        //             console.log(err.response)
        //         })
        // },

        // removeLectureFromFavorite({ commit }, lectureId) {
        //     axios.get(`/api/lectures/favorite/remove/${store.state.auth.user.id}/${lectureId}`)
        //         .then(res => {
        //             if (res.data.status === 'ok') {
        //                 commit('REMOVE_FAVORITE_LECTURE_ID', lectureId)
        //             }
        //         })
        //         .catch(err => {
        //             console.log(err.response)
        //         })
        // },
    }
}
