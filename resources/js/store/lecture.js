import moment from 'moment'

import axios from 'axios'
import store from '../store'
import router from '../router'

export default {
    namespaced: true,

    state: {
        lecture: {},

        lectures: [],
        searchedLectures: [],
        filteredLectures: [],
    },

    getters: {
        lecture(state) {
            return state.lecture
        },
        lectureById: (state) => (id) => {
            return state.lectures.find(lecture => lecture.id === parseInt(id, 10));
        },

        lectures(state) {
            return state.lectures
        },
        searchedLectures(state) {
            return state.searchedLectures
        },
        filteredLectures(state) {
            return state.filteredLectures
        },

        lectureIdByConferenceId: (state) => (conferenceId) => {
            const lecture = state.lectures.find(lecture => lecture.conference_id == conferenceId && lecture.user_id == store.state.auth.user.id)
            return lecture ? lecture.id : undefined
        },

        titleRules() {
            return [
                value => !!value || 'Title is required!',
                value => (value && value.length >= 2) || 'Title must be longer than 1 character!',
                value => (value && value.length <= 255) || 'Title must be less than 256 characters!',
            ]
        },
        descriptionRules() {
            return [
                value => !!value || 'Description is required!',
            ]
        },
        fileRules() {
            return [
                value => (!!value && !!value.length) || 'Presentation is required!',
                value => {
                    if (value && value.length) {
                        return value[0].type === 'application/vnd.openxmlformats-officedocument.presentationml.presentation' ||
                        value[0].type === 'application/vnd.ms-powerpoint' ||
                        'Presentation file type must be .ppt or .pptx!'
                    }
                    else {
                        return true
                    }
                },
                value => {
                    if (value && value.length) {
                        return value[0].size < 10000000 || 'Presentation size should be less than 10 MB!'
                    }
                    else {
                        return true
                    }
                },
            ]
        },

        isUserOwnThisLecture(state) {
            return parseInt(store.state.auth.user.id, 10) === parseInt(state.lecture.user_id, 10)
        },

        getFreeStartTime: () => (conference, isEditMode = false) => {
            const format = "HH:mm"

            const stepMinutes = 1
            const minLectureTime = 10

            let startLectureDateTime = new Date(conference.date_time_event)
            let endLectureDateTime = new Date(conference.date_time_event)
            endLectureDateTime.setMinutes(endLectureDateTime.getMinutes() + minLectureTime)

            while (!store.getters['lecture/isTimeFree'](conference.id, isEditMode, startLectureDateTime, endLectureDateTime).freeTimeAvailable) {
                startLectureDateTime.setMinutes(startLectureDateTime.getMinutes() + stepMinutes)
                endLectureDateTime.setMinutes(endLectureDateTime.getMinutes() + stepMinutes)
            }

            let deadlineForLecture = new Date(conference.date_time_event)
            deadlineForLecture.setHours(23)
            deadlineForLecture.setMinutes(50)

            if (startLectureDateTime < deadlineForLecture) {
                return moment(startLectureDateTime).format(format)
            }
            else {
                return false
            }
        },
        isTimeFree: (state) => (conferenceId, isEditMode, startDateTime, endDateTime = null) => {
            let result = { 'freeTimeAvailable': true }

            for (const lecture of state.lectures) {
                if (lecture.conference_id === conferenceId) {
                    if (isEditMode && lecture.id === state.lecture.id) {
                        continue
                    }

                    const lectureDateTimeStart = new Date(lecture.date_time_start)
                    const lectureDateTimeEnd = new Date(lecture.date_time_end)

                    if (!endDateTime) {
                        if (startDateTime >= lectureDateTimeStart && startDateTime <= lectureDateTimeEnd) {
                            result = { 'freeTimeAvailable': false, 'lecture': lecture }
                            break
                        }
                    }
                    else {
                        if (endDateTime >= lectureDateTimeStart && endDateTime <= lectureDateTimeEnd) {
                            result = { 'freeTimeAvailable': false, 'lecture': lecture }
                            break
                        }
                        else if (startDateTime <= lectureDateTimeStart && endDateTime >= lectureDateTimeEnd) {
                            result = { 'freeTimeAvailable': false, 'lecture': lecture }
                            break
                        }
                    }
                }
            }

            return result
        },
    },

    mutations: {
        SET_LECTURE (state, value) {
            state.lecture = value
        },

        SET_LECTURES (state, value) {
            state.lectures = value
        },

        SET_SEARCHED_LECTURES (state, value) {
            state.searchedLectures = value
        },

        SET_FILTERED_LECTURES (state, value) {
            state.filteredLectures = value
        },

        PUSH_LECTURE (state, value) {
            state.lectures.push(value)
        },

        UPDATE_LECTURE (state, value) {
            const index = state.lectures.map(lecture => lecture.id).indexOf(value.id);
            state.lectures.splice(index, 1, value);
        },

        REMOVE_LECTURE (state, id) {
            let index = state.lectures.map(lecture => lecture.id).indexOf(id);
            state.lectures.splice(index, 1);
        },

        COMMENT_INCREMENT (state, id) {
            const index = state.lectures.findIndex(lec => lec.id === id);
            state.lectures[index].comments_count++
        }
    },

    actions: {
        fetchAllLectures({ commit }) {
            axios.get('/api/lectures')
                .then(res => {
                    commit('SET_LECTURES', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        fetchFilteredLectures({ commit }, filter) {
            axios.post('/api/lectures/filtered', filter, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('SET_FILTERED_LECTURES', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        fetchLectureById({ commit }, id) {
            axios.get(`/api/lectures/${id}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('SET_LECTURE', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        searchLectures({ commit }, query) {
            axios.get(`/api/lectures/search/${query.search}/limit/${query.limit}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('SET_SEARCHED_LECTURES', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        storeLecture({ commit }, lecture) {
            axios.post('/api/lectures/add', lecture, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('PUSH_LECTURE', res.data)
                    store.dispatch('user_conferences/joinConference', res.data.conference_id)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        updateLecture({ commit }, lecture) {
            axios.post(`/api/lectures/${lecture.id}/update`, lecture, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('UPDATE_LECTURE', res.data)
                    router.go(-1)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        downloadPresentation({ commit }, id) {
            axios.get(`/api/lectures/${id}/presentation/download`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    const fileLink = document.createElement('a')

                    fileLink.href = URL.createObjectURL(new Blob([res.data]))
                    fileLink.setAttribute('download', 'file.pptx')
                    fileLink.click()

                    URL.revokeObjectURL(fileLink.href)
                })
                .catch(err => {
                    console.log(err)
                })
        },

        updateLectureCategories({ state, dispatch }, categories) {
            categories.forEach(category => {
                let lectures = state.lectures.filter(lect => lect.category_id === category.id)

                lectures.forEach(lect => {
                    lect.category_id = null
                    dispatch('updateLecture', lect)
                })
            })
        },

        deleteLecture({ commit }, lectureId) {
            axios.get(`/api/lectures/${lectureId}/delete`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('REMOVE_LECTURE', res.data.id)
                    store.dispatch('user_conferences/cancelParticipation', res.data.conference_id)

                    router.push({ name: 'conferences' })
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        incrementCommentsCount({ commit }, lectureId) {
            commit('COMMENT_INCREMENT', lectureId)
        },
    }
}
