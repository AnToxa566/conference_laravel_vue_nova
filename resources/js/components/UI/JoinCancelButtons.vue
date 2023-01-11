<template>
    <v-btn
        v-if="!isJoined && !this.isAnnouncer"
        variant="tonal" color="white" class="mx-1"
        @click="this.joinConference()"
    >
        Join
    </v-btn>

    <lecture-form-dialog
        v-else-if="!isJoined && this.isAnnouncer"
        :conference="this.conference"
    ></lecture-form-dialog>

    <v-btn
        v-else
        variant="text" color="white" class="mx-1"
        @click="this.confirmationDialog = true"
    >
        Сancel participation
    </v-btn>

    <action-confirmation
        v-model="confirmationDialog"

        title="Cancel participation?"
        text="Are you sure you want to cancel your participation in this conference?"

        @confirm="this.cancelParticipation"
    >
    </action-confirmation>
</template>


<script>
import LectureFormDialog from '../Lecture/LectureFormDialog.vue'
import userTypes from '../../config/user_types'

export default {
    name: 'join-cancel-buttons',

    data: () => ({
        dialog: false,
        confirmationDialog: false,
    }),

    components: {
        LectureFormDialog,
    },

    props: {
        conference: {
            type: Object,
            required: true,
        },

        isJoined: {
            type: Boolean,
            required: true,
        },
    },

    computed: {
        user() {
            return this.$store.getters['auth/user']
        },
        lectureId() {
            return this.$store.getters['lecture/lectureIdByConferenceId'](this.conference.id)
        },
        authenticated() {
            return this.$store.getters['auth/authenticated']
        },

        isAnnouncer() {
            return this.$store.getters['auth/user'].type == userTypes.ANNOUNCER
        },
    },

    methods: {
        joinConference() {
            if (this.authenticated) {
                if (!this.isJoined) {
                    this.$store.dispatch('user_conferences/joinConference', this.conference.id)
                }
            }
            else {
                this.$router.push('/login')
            }
        },

        cancelParticipation(event) {
            if (this.isJoined && event) {
                if (this.isAnnouncer) {
                    this.$store.dispatch('lecture/deleteLecture', this.lectureId)
                }
                else {
                    this.$store.dispatch('user_conferences/cancelParticipation', this.conference.id)
                }
            }
        },
    }
}
</script>
