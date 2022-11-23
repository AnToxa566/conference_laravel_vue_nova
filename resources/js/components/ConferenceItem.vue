<template>
    <v-hover
        v-slot="{ isHovering, props }"
        open-delay="200"
    >
        <v-card
            :elevation="isHovering ? 18 : 6"
            :class="{ 'on-hover': isHovering }"
            class="pa-4 mb-4"
            variant="tonal"
            v-bind="props"
        >
            <v-card-title class="text-h5"> {{ conference.title }} </v-card-title>

            <v-card-subtitle> {{ formatedDateTime }} </v-card-subtitle>
            <v-card-subtitle> {{ conference.country }} </v-card-subtitle>

            <v-card-actions class="d-flex justify-space-between">
                <div>
                    <v-btn v-if="!isJoined" variant="tonal" color="white" @click="this.joinConference()"> Join </v-btn>
                    <v-btn v-else variant="tonal" color="white" @click="this.cancelParticipation()"> Сancel participation </v-btn>

                    <v-btn variant="tonal" color="white" @click="$router.push(`/conferences/${conference.id}`)"> Details </v-btn>
                    <v-btn variant="tonal" color="white" @click="$router.push(`/conferences/${conference.id}/edit`)"> Update </v-btn>
                </div>

                <div v-if="isJoined">
                    <ShareNetwork
                        network="facebook"
                        v-bind:url="this.share_url"
                        v-bind:title="conference.title"
                        v-bind:description="this.share_text"
                        hashtags="bwt,quest"
                        class="text-decoration-none"
                    >
                        <v-icon class="mx-2" color="blue-darken-2" size="x-large" icon="mdi-facebook"/>
                    </ShareNetwork>

                    <ShareNetwork
                        network="twitter"
                        v-bind:url="this.share_url"
                        v-bind:title="this.share_text"
                        hashtags="bwt,quest"
                        class="text-decoration-none"
                    >
                        <v-icon class="mx-2" color="blue-darken-2" size="x-large" icon="mdi-twitter"/>
                    </ShareNetwork>
                </div>
            </v-card-actions>
        </v-card>
    </v-hover>
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

    data() {
        return {
            share_url: import.meta.env.VITE_SHARE_URI,
            share_text: import.meta.env.VITE_SHARE_TEXT,
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
            console.log(new URL(location.href))

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
    .v-card:not(.on-hover) {
        opacity: 0.8;
    }
</style>
