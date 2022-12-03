<template>
    <v-dialog
        v-model="dialog"
        max-height="100vh"
        persistent
    >
        <template
            v-slot:activator="{ props }"
        >
            <v-btn
                v-if="this.getFreeStartTime"
                variant="tonal" color="white" class="mx-1"
                v-bind="props"
            >
                Join
            </v-btn>

            <div
                v-else
                class="d-flex align-center mx-1"
            >
                <p class="mb-0">Registration is impossible</p>
            </div>
        </template>

        <v-container>
            <v-card class="pa-8">
                <v-card-title class="mb-6">
                    <span class="text-h4">Add Lecture</span>
                </v-card-title>

                <v-form
                    action=""
                    @submit="addLecture(lecture)"
                    ref="form"
                    v-model="valid"
                >
                    <v-row>

                        <!-- Title -->

                        <v-col
                            cols="12"
                        >
                            <v-text-field
                                v-model="lecture.title"
                                label="Lecture's title*"
                                variant="solo"
                                :counter="255"
                                :rules="titleRules"
                            ></v-text-field>
                        </v-col>

                        <!-- Start date/time -->

                        <v-col
                            cols="12"
                        >
                            <Datepicker
                                dark
                                timePicker
                                teleportCenter
                                class="w-100"
                                placeholder="When the lecture start*"
                                v-model="lecture.date_time_start"
                                :minTime="this.minLectureTime"
                                :maxTime="this.maxLectureTime"
                            />

                            <div class="message w-100">
                                <p class="mb-0" v-if="startTimeErrorMessage"> {{ this.startTimeErrorMessage }} </p>
                            </div>
                        </v-col>

                        <!-- End date/time -->

                        <v-col
                            cols="12"
                        >
                            <Datepicker
                                dark
                                timePicker
                                teleportCenter
                                class="w-100"
                                placeholder="When the lecture end*"
                                v-model="lecture.date_time_end"
                                :minTime="this.minLectureTime"
                                :maxTime="this.maxLectureTime"
                            />

                            <div class="message w-100">
                                <p class="mb-0" v-if="endTimeErrorMessage"> {{ this.endTimeErrorMessage }} </p>
                            </div>
                        </v-col>

                        <!-- Description -->

                        <v-col
                            cols="12"
                        >
                            <v-textarea
                                v-model="lecture.description"
                                label="Lecture's description*"
                                variant="solo"
                                :rules="descriptionRules"
                            ></v-textarea>
                        </v-col>

                        <!-- Presentation -->

                        <v-col
                            cols="12"
                        >
                            <v-file-input
                                show-size
                                label="Lecture's presentation"
                                variant="solo"
                                prepend-icon="mdi-presentation"
                                accept=".ppt, .pptx"
                                v-on:change="onFileChange"
                                :rules="fileRules"
                            ></v-file-input>
                        </v-col>
                    </v-row>

                    <small>*indicates required field</small>

                    <div class="d-flex justify-content-end">
                        <v-btn
                            variant="tonal" color="white" class="mx-2"
                            @click="dialog = false"
                        >
                            Close
                        </v-btn>

                        <v-btn
                            variant="tonal" color="white" class="mx-2"
                            @click.prevent="addLecture(lecture)"
                        >
                            Save
                        </v-btn>
                    </div>
                </v-form>
            </v-card>
        </v-container>
    </v-dialog>
</template>


<script>
import moment from 'moment'

export default {
    data: () => ({
        dialog: false,
        valid: false,

        lecture: {
            user_id: null,
            conference_id: null,

            title: '',
            date_time_start: '',
            date_time_end: '',

            description: null,
            presentation_path: null,
        },

        titleRules: [
            value => !!value || 'Title is required!',
            value => (value && value.length >= 2) || 'Title must be longer than 1 character!',
            value => (value && value.length <= 255) || 'Title must be less than 256 characters!',
        ],

        descriptionRules: [
            value => !!value || 'Description is required!',
        ],

        fileRules: [
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
        ],
    }),

    props: {
        conferenceId: {
            type: Number,
            required: true,
        },
    },

    computed: {
        conferenceById() {
            return this.$store.getters['conference/conferenceById'](this.conferenceId)
        },

        lectures() {
            return this.$store.getters['lecture/lectures']
        },

        userId() {
            return this.$store.getters['auth/user'].id
        },

        startTimeErrorMessage() {
            if (!this.lecture.date_time_start) {
                return 'Start time are required!'
            }

            const customerDateTimeStart = new Date(this.getFormatedDateTime(this.lecture.date_time_start))
            let errorMessage = ''

            if (!this.freeTimeCheck(customerDateTimeStart)) {
                const nearestFreeTime = this.getFreeStartTime
                errorMessage = 'At this time, you cannot start the lecture, as this time is already taken! Next free time: ' + nearestFreeTime
            }

            return errorMessage;
        },

        endTimeErrorMessage() {
            if (!this.lecture.date_time_end) {
                return 'End time are required!'
            }
            else if (!this.lecture.date_time_start) {
                return 'Input the start time of the lecture!'
            }

            const startTotalMinutes = this.lecture.date_time_start.hours * 60 + this.lecture.date_time_start.minutes
            const endTotalMinutes = this.lecture.date_time_end.hours * 60 + this.lecture.date_time_end.minutes

            if (endTotalMinutes - startTotalMinutes < 0) {
                return 'The end time cannot be earlier than the start time!'
            }
            else if (endTotalMinutes - startTotalMinutes < 10) {
                return 'The lecture cannot last less than 10 minutes!'
            }
            else if (endTotalMinutes - startTotalMinutes > 60) {
                return 'The lecture should last no more than 60 minutes!'
            }

            const customerDateTimeEnd = new Date(this.getFormatedDateTime(this.lecture.date_time_end))

            if (!this.freeTimeCheck(customerDateTimeEnd)) {
                return 'This time is already taken!'
            }

            return '';
        },

        minLectureTime() {
            const date = new Date(this.conferenceById.date_time_event)

            return {
                hours: date.getHours(),
                minutes: date.getMinutes(),
            }
        },
        maxLectureTime() {
            return {
                hours: 23,
                minutes: 59,
            }
        },

        getFreeStartTime() {
            const format = "HH:mm"

            const stepMinutes = 1
            const minLectureTime = 10

            let startLectureDateTime = new Date(this.conferenceById.date_time_event)
            let endLectureDateTime = new Date(this.conferenceById.date_time_event)
            endLectureDateTime.setMinutes(endLectureDateTime.getMinutes() + minLectureTime)

            while (!this.freeTimeCheck(startLectureDateTime) || !this.freeTimeCheck(endLectureDateTime)) {
                startLectureDateTime.setMinutes(startLectureDateTime.getMinutes() + stepMinutes)
                endLectureDateTime.setMinutes(endLectureDateTime.getMinutes() + stepMinutes)
            }

            let deadlineForLecture = new Date(this.conferenceById.date_time_event)
            deadlineForLecture.setHours(23)
            deadlineForLecture.setMinutes(50)

            if (startLectureDateTime < deadlineForLecture) {
                return moment(startLectureDateTime).format(format)
            }
            else {
                return false
            }
        },
    },

    methods: {
        onFileChange(event) {
            var files = event.target.files || event.dataTransfer.files

            if (!files.length) {
                return
            }

            this.lecture.presentation_path = files[0]
        },

        freeTimeCheck(customerDateTime) {
            let result = true

            this.lectures.forEach(lecture => {
                if (lecture.conference_id === this.conferenceId) {
                    const lectureDateTimeStart = new Date(lecture.date_time_start)
                    const lectureDateTimeEnd = new Date(lecture.date_time_end)

                    if (customerDateTime > lectureDateTimeStart && customerDateTime < lectureDateTimeEnd) {
                        result = false
                        return
                    }
                }
            });

            return result
        },

        getFormatedDateTime(lectureTime) {
            const format = "YYYY-MM-DD HH:mm:ss"
            let lectureDateTime = new Date(this.conferenceById.date_time_event)

            lectureDateTime.setHours(lectureTime.hours)
            lectureDateTime.setMinutes(lectureTime.minutes)

            return moment(lectureDateTime).format(format)
        },

        getFormatedLecture(lecture) {
            const data = new FormData();

            data.append('user_id', this.userId);
            data.append('conference_id', this.conferenceId);

            data.append('date_time_start', this.getFormatedDateTime(lecture.date_time_start));
            data.append('date_time_end', this.getFormatedDateTime(lecture.date_time_end));

            data.append('title', lecture.title);
            data.append('description', lecture.description);
            data.append('presentation_path', lecture.presentation_path);

            return data
        },

        async addLecture(lecture) {
            const { valid } = await this.$refs.form.validate()

            if (valid && !this.startTimeErrorMessage && !this.endTimeErrorMessage) {
                const formatedLecture = this.getFormatedLecture(lecture)

                this.$store.dispatch('lecture/storeLecture', formatedLecture)

                this.$store.dispatch('user_conferences/joinConference', {
                    'conference_id': this.conferenceId,
                    'user_id': this.userId,
                })

                this.dialog = false
            }
        },
    },
}
</script>


<style>
    .message {
        color: #cf6679;
        font-size: 12px;
        padding: 6px 16px 0 16px;
        margin-bottom: 0px;
    }
</style>
