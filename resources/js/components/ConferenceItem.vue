<template>
  <v-card class="pa-4 mb-4" variant="tonal"  @click.stop="$router.push(`/conferences/${conference.id}`)">
    <v-card-title class="text-h5"> {{ conference.title }} </v-card-title>

    <v-card-subtitle> {{ formatedDateTime }} </v-card-subtitle>
    <v-card-subtitle> {{ conference.country }} </v-card-subtitle>

    <v-card-actions>
      <v-btn v-if="!isJoined" variant="tonal" color="white" @click.stop="this.joinConference()"> Join </v-btn>
      <v-btn v-else variant="tonal" color="white" @click.stop="this.cancelParticipation()"> Сancel participation </v-btn>

      <v-btn variant="tonal" color="white" @click.stop="$router.push(`/conferences/${conference.id}`)"> Details </v-btn>
      <v-btn variant="tonal" color="white" @click.stop="$router.push(`/conferences/${conference.id}/edit`)"> Update </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
    props: {
        conference: {
            type: Object,
            required: true,
        },
        isJoined: {
            type: Boolean,
            required: true,
        }
    },

    computed: {
        authenticated() {
            return this.$store.getters['auth/authenticated']
        },
        user() {
            return this.$store.getters['auth/user']
        },

        formatedDateTime() {
            return this.$store.getters['conference/formatedDateTime'](this.conference.id)
        },
    },

    methods: {
        joinConference() {
            if (this.authenticated) {
                if (!this.isJoined) {
                    this.$store.dispatch('user_conferences/joinConference', {
                        'conference_id': this.conference.id,
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
                    'conference_id': this.conference.id,
                    'user_id': this.user.id,
                })
            }
        },
    },
};
</script>

<style scoped>
</style>
