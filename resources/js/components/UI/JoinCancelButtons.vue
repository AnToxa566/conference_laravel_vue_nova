<template>
    <v-btn
        v-if="!isJoined"
        variant="tonal" color="white" class="mx-1"
        @click="this.joinValidation"
    >
        Join
    </v-btn>

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

        @confirm="this.cancelParticipationValidation"
    >
    </action-confirmation>

    <lecture-form-dialog
        v-model="lectureFormDialog"
        :conference="this.conference"
    ></lecture-form-dialog>
</template>


<script>
import LectureFormDialog from '../Lecture/LectureFormDialog.vue'
import userTypes from '../../config/user_types'

import { mapGetters, mapActions } from 'vuex'

export default {
    name: 'join-cancel-buttons',

    data: () => ({
        lectureFormDialog: false,
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
        ...mapGetters({
            user: 'auth/user',
            lectureId: 'lecture/lectureIdByConferenceId',
            authenticated: 'auth/authenticated',
        }),

        isAnnouncer() {
            return this.user.type == userTypes.ANNOUNCER
        },

        isJoinsEnded() {
            return this.user.joins_left === 0
        },
    },

    methods: {
        ...mapActions({
            deleteLecture: 'lecture/deleteLecture',
            joinConference: 'user_conferences/joinConference',
            cancelParticipation: 'user_conferences/cancelParticipation',
        }),

        joinValidation() {
            if (!this.authenticated) {
                this.$router.push({ name: 'login' })
                return
            }

            if (this.isJoinsEnded) {
                this.$router.push({
                    name: 'plans',
                    query: { joinsEnded: true },
                })

                return
            }

            if (this.isJoined) {
                return
            }

            if (this.isAnnouncer) {
                this.lectureFormDialog = true
                return
            }

            this.joinConference(this.conference.id)
        },

        cancelParticipationValidation(isConfirmed) {
            if (!this.isJoined || !isConfirmed) {
                return
            }

            if (this.isAnnouncer) {
                this.deleteLecture(this.lectureId(this.conference.id))
                return
            }

            this.cancelParticipation(this.conference.id)
        },
    }
}
</script>
