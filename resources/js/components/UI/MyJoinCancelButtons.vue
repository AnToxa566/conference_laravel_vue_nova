<template>
    <v-btn v-if="!isJoined" variant="tonal" color="white" class="ms-0" @click="this.joinConference()"> Join </v-btn>
    <v-btn v-else variant="tonal" color="white" class="ms-0" @click="this.cancelParticipation()"> Сancel participation </v-btn>
</template>


<script>
export default {
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
        authenticated() {
            return this.$store.getters['auth/authenticated']
        },
        user() {
            return this.$store.getters['auth/user']
        },
    },

    methods: {
        joinConference() {
            if (this.authenticated) {
                if (!this.isJoined) {
                    this.$store.dispatch('user_conferences/joinConference', {
                        'conference_id': this.conferenceId,
                        'user_id': this.user.id,
                    })
                }
            }
            else {
                this.$router.push('/login')
            }
        },

        cancelParticipation() {
            if (this.isJoined) {
                this.$store.dispatch('user_conferences/cancelParticipation', {
                    'conference_id': this.conferenceId,
                    'user_id': this.user.id,
                })
            }
        },
    }
}
</script>


<style scoped>

</style>
