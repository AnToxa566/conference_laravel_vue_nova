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

    <!-- Buttons for owner this lecture -->

    <div
        v-if="isUserOwnThisLecture"
        class="d-flex"
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
        class="d-flex"
    >
        <v-spacer></v-spacer>

        <v-btn class="mb-6" variant="tonal" color="red" @click="this.confirmationDialog = true"> Delete </v-btn>

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

        conferenceId: null,
        lectureId: null,
    }),

    created() {
        this.conferenceId = parseInt(this.$route.params.conference_id, 10);
        this.lectureId = parseInt(this.$route.params.lecture_id, 10);

        this.$store.dispatch('conference/fetchDetailConference', this.conferenceId)
        this.$store.dispatch('lecture/fetchLectureById', this.lectureId)
    },

    computed: {
        conference() {
            return this.$store.getters['conference/conference']
        },
        lecture() {
            return this.$store.getters['lecture/lecture']
        },
        category() {
            return this.$store.getters['category/categoryById'](this.lecture.category_id)
        },

        userId() {
            return this.$store.getters['auth/user'].id
        },
        isAdmin() {
            return this.$store.getters['auth/isAdmin']
        },

        isUserOwnThisLecture() {
            return this.$store.getters['lecture/isUserOwnThisLecture']
        },

        formattedDate() {
            const format = 'YYYY-MM-DD'
            const date = new Date(this.lecture.date_time_start)

            return moment(date).format(format)
        },
        startFormattedTime() {
            const format = 'HH:mm'
            const date = new Date(this.lecture.date_time_start)

            return moment(date).format(format)
        },
        endFormattedTime() {
            const format = 'HH:mm'
            const date = new Date(this.lecture.date_time_end)

            return moment(date).format(format)
        },
    },

    methods: {
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
