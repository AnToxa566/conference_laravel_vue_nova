import axios from 'axios'
import store from '../store'

export default {
    namespaced: true,

    state: {
        favoritedLecturesId: [],
    },

    getters: {
        favoritedLecturesId: state => state.favoritedLecturesId,

        hasFavoritedLectures: state => state.favoritedLecturesId.length > 0,

        isLectureFavoritedById: state => lectureId => state.favoritedLecturesId.includes(lectureId),
    },

    mutations: {
        storeFavoritedLecturesId (state, value) {
            state.favoritedLecturesId = value
        },

        pushFavoritedLectureId (state, id) {
            state.favoritedLecturesId.push(id)
        },

        deleteFavoritedLectureId (state, id) {
            const index = state.favoritedLecturesId.indexOf(parseInt(id, 10));
            state.favoritedLecturesId.splice(index, 1);
        },
    },

    actions: {
        fetchFavoritedLecturesId({ commit }) {
            axios.get(`/api/lectures/favorited/${store.state.auth.user.id}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storeFavoritedLecturesId', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        addLectureToFavorite({ commit }, lectureId) {
            axios.get(`/api/lectures/favorite/add/${store.state.auth.user.id}/${lectureId}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('pushFavoritedLectureId', lectureId)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        removeLectureFromFavorite({ commit }, lectureId) {
            axios.get(`/api/lectures/favorite/remove/${store.state.auth.user.id}/${lectureId}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('deleteFavoritedLectureId', lectureId)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
