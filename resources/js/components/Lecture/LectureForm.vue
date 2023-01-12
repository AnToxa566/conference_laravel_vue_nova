<template>
    <v-form
        action=""
        @submit="onSubmit(lecture)"
        ref="form"
        v-model="valid"
    >
        <!-- Title field -->

        <v-text-field
            v-model="lecture.title"

            label="Lecture's title*"
            variant="solo"
            class="mb-4"

            :counter="255"
            :rules="titleRules"
        ></v-text-field>


        <!-- Lecture start time picker -->

        <custom-timepicker
            v-model="lecture.date_time_start"

            :min="this.minLectureTime"
            :max="this.maxLectureTime"
            :hint="this.startTimeErrorMessage"

            placeholder="When the lecture start*"
            class="mb-4"
        >
        </custom-timepicker>


        <!-- Lecture end time picker -->

        <custom-timepicker
            v-model="lecture.date_time_end"

            :min="this.minLectureTime"
            :max="this.maxLectureTime"
            :hint="this.endTimeErrorMessage"

            placeholder="When the lecture end*"
            class="mb-4"
        >
        </custom-timepicker>


        <!-- Description textarea -->

        <v-textarea
            v-model="lecture.description"

            label="Lecture's description*"
            variant="solo"
            class="mb-4"

            :rules="descriptionRules"
        ></v-textarea>


        <!-- Category Selected -->

        <category-selected
            v-if="this.conference.category_id"

            :roots="this.roots"
            :nodes="this.nodes"

            :defaultSelect="{
                text: this.category ? this.category.title : '',
                id: this.category ? this.category.id : '',
            }"

            @select="categorySelected"
            @clear="categoryClear"
        >
        </category-selected>


        <!-- Presentation file input -->

        <v-file-input
            v-if="!this.isEditMode"
            v-on:change="onFileChange"
            :rules="fileRules"

            label="Lecture's presentation*"
            accept=".ppt, .pptx"
            variant="solo"

            show-size
        ></v-file-input>


        <!-- Presentation download link -->

        <div
            v-else
            class="d-flex mb-8"
        >
            <v-icon icon="mdi-monitor-arrow-down-variant" class="mx-2"></v-icon>
            <span
                class="text-decoration-underline mx-2"
                style="cursor: pointer;"
                @click="downloadPresentation"
            >
                {{ this.lectureToEdit.presentation_name }}
            </span>
        </div>


        <!-- Online checkbox -->

        <v-checkbox
            v-if="!this.isEditMode"
            v-model="lecture.isOnline"
            color="white"
            label="Online"
            class="mb-4"

            hide-details
        ></v-checkbox>

        <v-alert
            v-if="lecture.isOnline"

            title="Zoom start link"
            variant="tonal"
            icon="mdi-link"
            class="mb-4"

            prominent
        >
            <span class="text-subtitle-2">10 minutes before the start of the lecture, the page of lecture's details will contain a link to the start of the zoom meeting.</span>
        </v-alert>


        <!-- User Hints -->

        <small>*indicates required field</small>


        <!-- Buttons -->

        <div class="d-flex justify-content-end">
            <slot name="extraButtons"></slot>

            <v-btn
                variant="tonal" color="white" class="mx-1"
                @click.prevent="onSubmit(lecture)"
            >
                Save
            </v-btn>
        </div>
    </v-form>
</template>


<script>
import lecture from '../../config/lecture'
import moment from 'moment'
import { computed } from 'vue'
import { useStore } from 'vuex'

export default {
    setup () {
        const store = useStore()

        const titleRules = computed(() => store.getters['lecture/titleRules'])
        const descriptionRules = computed(() => store.getters['lecture/descriptionRules'])
        const fileRules = computed(() => store.getters['lecture/fileRules'])

        const lectures = computed(() => store.getters['lecture/lectures'])

        const userId = computed(() => store.getters['auth/user'].id)

        return {
            lectures,

            titleRules,
            descriptionRules,
            fileRules,

            userId,
        }
    },

    emits: [
        'submit',
    ],

    data: () => ({
        valid: false,
        isEditMode: false,

        lecture: {
            title: '',
            description: '',

            date_time_start: null,
            date_time_end: null,

            presentation: null,
            category_id: null,

            isOnline: false,
        },
    }),

    props: {
        conferenceId: {
            type: Number,
            required: true,
        },
        lectureToEdit: {
            type: Object,
            required: false,
        },
    },

    created() {
        this.$store.dispatch('conference/fetchDetailConference', this.conferenceId)

        if (this.lectureToEdit) {
            this.lecture = this.lectureToEdit

            const startDateTime = new Date(this.lectureToEdit.date_time_start)
            const endtDateTime = new Date(this.lectureToEdit.date_time_end)

            this.lecture.date_time_start = {
                hours: startDateTime.getHours(),
                minutes: startDateTime.getMinutes(),
            }
            this.lecture.date_time_end = {
                hours: endtDateTime.getHours(),
                minutes: endtDateTime.getMinutes(),
            }

            this.$store.dispatch('lecture/fetchLectureById', this.lectureToEdit.id)
            this.isEditMode = true
        }
    },

    mounted() {
        if (this.conference.category_id) {
            this.$store.dispatch('category/fetchBranche', this.conference.category_id)
        }
    },

    computed: {
        conference() {
            return this.$store.getters['conference/conference']
        },
        category() {
            return this.$store.getters['category/categoryById'](this.lecture.category_id)
        },

        roots() {
            return this.$store.getters['category/lectureRoots']
        },

        nodes() {
            return this.$store.getters['category/lectureNodes']
        },

        getFreeStartTime() {
            return this.$store.getters['lecture/getFreeStartTime'](this.conference, this.isEditMode)
        },

        startTimeErrorMessage() {
            if (!this.lecture.date_time_start) {
                return 'Start time are required!'
            }

            const customerDateTimeStart = new Date(this.getFormatedDateTime(this.lecture.date_time_start))
            const checkFreeTime = this.isTimeFree(customerDateTimeStart)

            if (!checkFreeTime.freeTimeAvailable) {
                const nearestFreeTime = this.getFreeStartTime

                return this.getTimeErrorMessage(checkFreeTime.lecture) + ' The nearest free time: ' + nearestFreeTime
            }

            return '';
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
            else if (endTotalMinutes - startTotalMinutes < lecture.MIN_LECTURE_DURATION) {
                return `The lecture cannot last less than ${lecture.MIN_LECTURE_DURATION} minutes!`
            }
            else if (endTotalMinutes - startTotalMinutes > lecture.MAX_LECTURE_DURATION) {
                return `The lecture should last no more than ${lecture.MAX_LECTURE_DURATION} minutes!`
            }

            const customerDateTimeStart = new Date(this.getFormatedDateTime(this.lecture.date_time_start))
            const customerDateTimeEnd = new Date(this.getFormatedDateTime(this.lecture.date_time_end))
            const checkFreeTime = this.isTimeFree(customerDateTimeStart, customerDateTimeEnd)

            if (!checkFreeTime.freeTimeAvailable) {
                return this.getTimeErrorMessage(checkFreeTime.lecture)
            }

            return '';
        },

        minLectureTime() {
            const date = new Date(this.conference.date_time_event)

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
    },

    methods: {
        onFileChange(event) {
            const files = event.target.files || event.dataTransfer.files

            if (!files.length) {
                return
            }

            this.lecture.presentation = files[0]
        },

        downloadPresentation() {
            this.$store.dispatch('lecture/downloadPresentation', {
                id: this.lectureToEdit.id,
                presentationName: this.lectureToEdit.presentation_name,
            })
        },

        isTimeFree(startDateTime, endDateTime = null) {
            return this.$store.getters['lecture/isTimeFree'](this.conference.id, this.isEditMode, startDateTime, endDateTime)
        },

        getTimeErrorMessage(lecture) {
            const format = 'HH:mm'

            const lectureDateTimeStart = new Date(lecture.date_time_start)
            const lectureDateTimeEnd = new Date(lecture.date_time_end)

            return 'This time is already taken by the lecture "' + lecture.title + '" (' +
                moment(lectureDateTimeStart).format(format) + ' - ' + moment(lectureDateTimeEnd).format(format) +
                ')!'
        },

        getFormatedDateTime(lectureTime) {
            const format = "YYYY-MM-DD HH:mm:ss"
            let lectureDateTime = new Date(this.conference.date_time_event)

            lectureDateTime.setHours(lectureTime.hours)
            lectureDateTime.setMinutes(lectureTime.minutes)

            return moment(lectureDateTime).format(format)
        },

        getFormatedLecture(lecture) {
            const data = new FormData();

            data.append('user_id', this.userId)
            data.append('conference_id', this.conference.id)
            data.append('category_id', lecture.category_id ? lecture.category_id : '')

            data.append('date_time_start', this.getFormatedDateTime(lecture.date_time_start))
            data.append('date_time_end', this.getFormatedDateTime(lecture.date_time_end))

            data.append('title', lecture.title)
            data.append('description', lecture.description)
            data.append('presentation', lecture.presentation)

            data.append('is_online', lecture.isOnline ? '1' : '0')

            return data
        },

        categorySelected(event) {
            this.lecture.category_id = parseInt(event.id, 10)
        },

        categoryClear(event) {
            this.lecture.category_id = null
        },

        async onSubmit(lecture) {
            const { valid } = await this.$refs.form.validate()

            if (valid && !this.startTimeErrorMessage && !this.endTimeErrorMessage) {
                const formatedLecture = this.getFormatedLecture(lecture)

                this.$emit('submit', formatedLecture)
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
