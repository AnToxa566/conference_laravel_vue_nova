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

        error: '',
    },


    getters: {
        lecture: state => state.lecture,

        lectureById: state => id => state.lectures.find(lecture => lecture.id == parseInt(id, 10)),

        lectures: state => state.lectures,

        searchedLectures: state => state.searchedLectures,

        filteredLectures: state => state.filteredLectures,

        error: state => state.error,

        lectureIdByConferenceId: state => conferenceId => {
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
                        return value[0].type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation' ||
                        value[0].type == 'application/vnd.ms-powerpoint' ||
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

        isUserOwnThisLecture: state => id => store.state.auth.user.id == state.lectures.find(lecture => lecture.id == id).user_id,

        getFreeStartTime: () => (conference, isEditMode = false) => {
            const format = "HH:mm"

            const stepMinutes = 1
            const minLectureTime = 1

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

        isTimeFree: state => (conferenceId, isEditMode, startDateTime, endDateTime = null) => {
            let result = { 'freeTimeAvailable': true }

            for (const lecture of state.lectures) {
                if (parseInt(lecture.conference_id, 10) == parseInt(conferenceId, 10)) {
                    if (isEditMode && lecture.id == state.lecture.id) {
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
        storeLecture (state, value) {
            state.lecture = value
        },

        storeLectures (state, value) {
            state.lectures = value
        },

        storeSearchedLectures (state, value) {
            state.searchedLectures = value
        },

        storeFilteredLectures (state, value) {
            state.filteredLectures = value
        },

        storeError (state, value) {
            state.error = value
        },

        pushLecture (state, value) {
            state.lectures.push(value)
        },

        updateLecture (state, value) {
            const index = state.lectures.map(lecture => lecture.id).indexOf(value.id);
            state.lectures.splice(index, 1, value);
        },

        deleteLecture (state, id) {
            let index = state.lectures.map(lecture => lecture.id).indexOf(id);
            state.lectures.splice(index, 1);
        },

        incrementCommentCount (state, id) {
            const index = state.lectures.findIndex(lec => lec.id == id);
            state.lectures[index].comments_count++
        }
    },


    actions: {
        fetchAllLectures({ commit }) {
            axios.get('/api/lectures', JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storeLectures', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        fetchFilteredLectures({ commit }, filter) {
            axios.post('/api/lectures/filtered', filter, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storeFilteredLectures', res.data)
                    commit('storeError', '')
                })
                .catch(err => {
                    console.log(err.response)
                    commit('storeError', err.response.data.message)
                })
        },

        fetchLectureById({ commit }, id) {
            axios.get(`/api/lectures/${id}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storeLecture', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        searchLectures({ commit }, query) {
            axios.get(`/api/lectures/search/${query.search}/limit/${query.limit}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storeSearchedLectures', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        storeLecture({ commit }, lecture) {
            axios.post('/api/lectures/add', lecture, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('pushLecture', res.data.lecture)
                    commit('storeError', '')

                    store.dispatch('user_conferences/fetchDataAfterJoinConference')
                    store.commit('conference/incrementLectureCount', res.data.lecture.conference_id)

                    if (res.data.lecture.is_online != false) {
                        store.commit('meeting/storeMeeting', res.data.meeting)
                    }
                })
                .catch(err => {
                    console.log(err)
                    commit('storeError', err.response.data.message)
                })
        },

        updateLecture({ commit }, lecture) {
            axios.post(`/api/lectures/${lecture.id}/update`, lecture, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('updateLecture', res.data)
                    commit('storeError', '')
                    router.go(-1)
                })
                .catch(err => {
                    console.log(err.response)
                    commit('storeError', err.response.data.message)
                })
        },

        downloadPresentation({ }, query) {
            axios.get(`/api/lectures/${query.id}/presentation/download`, { ...JSON.parse(localStorage.getItem('config')), ...{ responseType: 'blob' }})
                .then(res => {
                    const blob = new Blob([res.data])
                    const fileLink = document.createElement('a')

                    fileLink.href = URL.createObjectURL(blob)
                    fileLink.download = query.presentationName
                    fileLink.click()
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        deleteLecture({ commit }, lectureId) {
            axios.get(`/api/lectures/${lectureId}/delete`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('deleteLecture', res.data.id)

                    store.commit('user_conferences/deleteJoinedConferencesId', res.data.conference_id)
                    store.commit('conference/decrementLectureCount', res.data.conference_id)

                    router.push({ name: 'conferences' })
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        incrementCommentsCount({ commit }, lectureId) {
            commit('incrementCommentCount', lectureId)
        },
    }
}
