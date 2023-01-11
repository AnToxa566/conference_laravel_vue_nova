<template>

    <!-- Header -->

    <custom-header>
        <template v-slot:header>Lecture details</template>
    </custom-header>


    <!-- Title -->

    <info-card>
        <template v-slot:header> Topic </template>
        <template v-slot:body> {{ this.lecture.title }} </template>
    </info-card>


    <!-- Date -->

    <info-card>
        <template v-slot:header> Date </template>
        <template v-slot:body> {{ this.formattedDate }} </template>
    </info-card>


    <!-- Time -->

    <info-card>
        <template v-slot:header> Time </template>
        <template v-slot:body>
            from <span class="font-weight-bold">{{ this.startFormattedTime }}</span>
            to <span class="font-weight-bold">{{ this.endFormattedTime }}</span>
        </template>
    </info-card>


    <!-- Description -->

    <info-card>
        <template v-slot:header> Description </template>
        <template v-slot:body> {{ this.lecture.description }} </template>
    </info-card>


    <!-- Presentation -->

    <info-card>
        <template v-slot:header> Presentation </template>
        <template v-slot:body>
            <span
                class="text-decoration-underline"
                style="cursor: pointer;"
                @click="downloadPresentation"
            >
                {{ this.lecture.presentation_name }}
            </span>
        </template>
    </info-card>


    <!-- Category -->

    <info-card
        v-if="this.category"
    >
        <template v-slot:header> Category </template>
        <template v-slot:body> {{ this.category.title }} </template>
    </info-card>


    <!-- Timer & Zoom Link -->

    <info-card
        v-if="this.lecture.is_online && this.isJoined"
    >
        <template v-slot:header> Zoom Link </template>

        <template v-slot:body>

            <!-- Timer -->
            <div v-if="this.isLectureWaiting">
                Show up in
                <custom-timer
                    :toTimestamp="this.getStartLectureTimestamp"
                    class="font-weight-bold"
                    @timerStoped="lectureStarted"
                ></custom-timer>
            </div>

            <!-- Zoom Link -->
            <div v-else-if="this.isLectureStarted">
                <a
                    class="text-white"
                    target="_blank"

                    :href="this.meetingUrl"
                >
                    Join zoom meeting
                </a>

                <custom-timer
                    :visible="false"
                    :toTimestamp="this.getEndLectureTimestamp"
                    @timerStoped="lectureEnded"
                ></custom-timer>
            </div>

            <!-- Lecture Ended -->
            <div v-else-if="this.isLectureEnded">
                The lecture is already over.
            </div>
        </template>
    </info-card>


    <!-- Buttons for owner this lecture -->

    <div
        v-if="isUserOwnThisLecture"
        class="d-flex mb-6"
    >
        <v-btn variant="tonal" color="white" class="mx-1" @click="$router.push(`/conferences/${this.conferenceId}/lectures/${this.lectureId}/edit`)"> Edit </v-btn>

        <join-cancel-buttons
            :isJoined="true"
            :conference="this.conference"
        ></join-cancel-buttons>
    </div>

    <!-- Buttons for admins -->

    <div
        v-if="isAdmin"
        class="d-flex mb-6"
    >
        <v-spacer></v-spacer>

        <v-btn variant="tonal" color="red" @click="this.confirmationDialog = true"> Delete </v-btn>

        <action-confirmation
            v-model="confirmationDialog"

            title="Delete lecture?"
            text="Are you sure you want to delete this lecture?"

            @confirm="this.delete"
        >
        </action-confirmation>
    </div>

    <!-- Comments Form and List-->

    <div class="d-flex justify-space-between align-center">
        <span class="py-3 text-h6 font-weight-bold"> Comments </span>

        <export-button
            v-if="isAdmin"
            @startExport="exportComments"
        >
            <template v-slot:title> Export comments </template>
        </export-button>
    </div>

    <comment-form
        :lecture_id="this.lectureId"
        @submit="storeComment"
    ></comment-form>

    <comment-list
        :lecture_id="this.lectureId"
    ></comment-list>
</template>


<script>
import CommentForm from '../../components/Comment/CommentForm.vue'
import CommentList from '../../components/Comment/CommentList.vue'
import moment from 'moment'

export default {
    components: {
        CommentForm,
        CommentList,
    },

    data: () => ({
        confirmationDialog: false,
        isLectureWaiting: false,
        isLectureStarted: false,
        isLectureEnded: false,

        conferenceId: null,
        lectureId: null,

        lecture: null,
    }),

    created() {
        this.conferenceId = parseInt(this.$route.params.conference_id, 10);
        this.lectureId = parseInt(this.$route.params.lecture_id, 10);

        this.$store.dispatch('conference/fetchDetailConference', this.conferenceId)
        this.$store.dispatch('lecture/fetchLectureById', this.lectureId)

        this.lecture = this.$store.getters['lecture/lectureById'](this.lectureId)
    },

    mounted() {
        this.isLectureWaiting = Date.now() < this.getStartLectureTimestamp
        this.isLectureStarted = Date.now() > this.getStartLectureTimestamp && Date.now() < this.getEndLectureTimestamp
        this.isLectureEnded = Date.now() > this.getEndLectureTimestamp
    },

    computed: {
        conference() {
            return this.$store.getters['conference/conference']
        },
        category() {
            return this.$store.getters['category/categoryById'](this.lecture.category_id)
        },
        meeting() {
            return this.$store.getters['meeting/meetings'].find(m => m.lecture_id == this.lecture.id)
        },
        meetingUrl() {
            return this.isUserOwnThisLecture ? this.meeting.start_url : this.meeting.join_url
        },

        userId() {
            return this.$store.getters['auth/user'].id
        },

        isAdmin() {
            return this.$store.getters['auth/isAdmin']
        },
        isJoined() {
            return this.$store.getters['user_conferences/joinedConferencesId'].includes(parseInt(this.conferenceId, 10))
        },
        isUserOwnThisLecture() {
            return this.$store.getters['lecture/isUserOwnThisLecture'](this.lectureId)
        },

        formattedDate() {
            return moment(new Date(this.lecture.date_time_start)).format('YYYY-MM-DD')
        },
        startFormattedTime() {
            return moment(new Date(this.lecture.date_time_start)).format('HH:mm')
        },
        endFormattedTime() {
            return moment(new Date(this.lecture.date_time_end)).format('HH:mm')
        },

        getStartLectureTimestamp() {
            return this.isUserOwnThisLecture ? this.announcerStartLectureTimestamp : this.startLectureTimestamp
        },
        announcerStartLectureTimestamp() {
            return this.startLectureTimestamp - (10 * 60 * 1000)
        },
        startLectureTimestamp() {
            return new Date(this.lecture.date_time_start).getTime()
        },

        getEndLectureTimestamp() {
            return new Date(this.lecture.date_time_end).getTime()
        },
    },

    methods: {
        lectureStarted() {
            this.isLectureWaiting = false
            this.isLectureStarted = true
        },

        lectureEnded() {
            this.isLectureStarted = false
            this.isLectureEnded = true
        },

        downloadPresentation() {
            this.$store.dispatch('lecture/downloadPresentation', {
                id: this.lecture.id,
                presentationName: this.lecture.presentation_name,
            })
        },

        exportComments() {
            this.$store.dispatch('lecture/exportComments', this.lectureId)
        },

        delete(event) {
            if (event) {
                this.$store.dispatch('lecture/deleteLecture', this.lectureId)
            }
        },

        async storeComment(comment) {
            this.$store.dispatch('comment/storeComment', comment)
        }
    }
}
</script>
