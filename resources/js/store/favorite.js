import axios from 'axios'
import store from '../store'

export default {
    namespaced: true,

    state: {
        favoritedLecturesId: [],
    },

    getters: {
        favoritedLecturesId(state) {
            return state.favoritedLecturesId
        },

        isLectureFavorited: (state) => (lectureId) => {
            return state.favoritedLecturesId.includes(lectureId)
        },
    },

    mutations: {
        SET_FAVORITE_LECTURES_ID (state, value) {
            state.favoritedLecturesId = value
        },

        PUSH_FAVORITE_LECTURE_ID (state, id) {
            state.favoritedLecturesId.push(id)
        },

        REMOVE_FAVORITE_LECTURE_ID (state, id) {
            const index = state.favoritedLecturesId.indexOf(parseInt(id, 10));
            state.favoritedLecturesId.splice(index, 1);
        },
    },

    actions: {
        fetchFavoritedLecturesId({ commit }) {
            axios.get(`/api/lectures/favorited/${store.state.auth.user.id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_FAVORITE_LECTURES_ID', res.data.lecturesId)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        addLectureToFavorite({ commit }, lectureId) {
            console.log('lectureId ' + lectureId + ' userId ' + store.state.auth.user.id)

            axios.get(`/api/lectures/favorite/add/${store.state.auth.user.id}/${lectureId}`)
                .then(res => {
                    console.log(res.data)
                    if (res.data.status === 'ok') {
                        commit('PUSH_FAVORITE_LECTURE_ID', lectureId)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        removeLectureFromFavorite({ commit }, lectureId) {
            axios.get(`/api/lectures/favorite/remove/${store.state.auth.user.id}/${lectureId}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('REMOVE_FAVORITE_LECTURE_ID', lectureId)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
