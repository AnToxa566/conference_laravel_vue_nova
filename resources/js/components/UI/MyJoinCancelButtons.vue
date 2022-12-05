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
        :conferenceId="this.conferenceId"
    ></lecture-form-dialog>

    <v-btn
        v-else
        variant="tonal" color="white" class="mx-1"
        @click="this.cancelParticipation()"
    >
        Сancel participation
    </v-btn>
</template>


<script>
import LectureFormDialog from '../LectureFormDialog.vue'
import userTypes from '../../config/user_types'

export default {
    name: 'my-join-cancel-buttons',

    data() {
        return {
            dialog: false,
        }
    },

    components: {
        LectureFormDialog,
    },

    props: {
        conferenceId: {
            type: Number,
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
        authenticated() {
            return this.$store.getters['auth/authenticated']
        },

        isAnnouncer() {
            return this.$store.getters['auth/user'].type === userTypes.ANNOUNCER
        },
    },

    methods: {
        joinConference() {
            if (this.authenticated) {
                if (!this.isJoined) {
                    this.$store.dispatch('user_conferences/joinConference', this.conferenceId)
                }
            }
            else {
                this.$router.push('/login')
            }
        },

        cancelParticipation() {
            if (this.isJoined) {
                this.$store.dispatch('user_conferences/cancelParticipation', this.conferenceId)

                if (this.isAnnouncer) {
                    this.$store.dispatch('lecture/deleteLecture', this.conferenceId)
                }
            }
        },
    }
}
</script>


<style scoped>

</style>
