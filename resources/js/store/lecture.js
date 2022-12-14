import moment from 'moment'

import axios from 'axios'
import store from '../store'
import router from '../router'

export default {
    namespaced: true,

    state: {
        lecture: {},
        lectures: [],

        commentsCounts: [],
    },

    getters: {
        lecture(state) {
            return state.lecture
        },
        lectures(state) {
            return state.lectures
        },

        commentsCounts(state) {
            return state.commentsCounts
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

        SET_COMMENTS_COUNTS (state, lectures) {
            state.commentsCounts = []

            lectures.forEach(lecture => {
                state.commentsCounts.push({
                    'lecture_id': lecture.id,
                    'comments_count': lecture.comments_count ? lecture.comments_count : 0,
                })
            })
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

        COMMENT_INCREMENT (state, lectureId) {
            const counterIndex = state.commentsCounts.findIndex(counter => counter.lecture_id === lectureId);
            state.commentsCounts[counterIndex].comments_count++
        }
    },

    actions: {
        fetchAllLectures({ commit }) {
            axios.get('/api/lectures')
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_LECTURES', res.data.lectures)
                        commit('SET_COMMENTS_COUNTS', res.data.lectures)
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
                        commit('PUSH_LECTURE', res.data.lecture)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        updateLecture({ commit }, lecture) {
            axios.post(`/api/lectures/${lecture.id}/update`, lecture)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('UPDATE_LECTURE', res.data.lecture)
                        router.go(-1)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        deleteLecture({ commit }, conference_id) {
            axios.get(`/api/lectures/delete/${store.state.auth.user.id}/${conference_id}`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('REMOVE_LECTURE', res.data.lecture_id)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        incrementCommentsCount({ commit }, lecture_id) {
            commit('COMMENT_INCREMENT', lecture_id)
        },

        cancelParticipation({ commit }, conference_id) {
            store.dispatch('user_conferences/cancelParticipation', conference_id)
            store.dispatch('lecture/deleteLecture', conference_id)

            router.push({ name: 'conferences' })
        },
    }
}
