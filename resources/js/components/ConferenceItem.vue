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
                <div class="d-flex">
                    <v-btn variant="tonal" color="white" class="mx-1" @click="$router.push(`/conferences/${conference.id}`)"> Details </v-btn>
                    <v-btn variant="tonal" color="white" class="mx-1" @click="$router.push(`/conferences/${conference.id}/lectures`)"> Lectures </v-btn>

                    <my-join-cancel-buttons
                        v-if="!isAdmin"
                        :isJoined="this.isJoined"
                        :conferenceId="this.conference.id"
                    ></my-join-cancel-buttons>
                    <div v-else>
                        <v-btn variant="tonal" color="white" @click="$router.push(`/conferences/${conference.id}/edit`)"> Update </v-btn>
                        <v-btn variant="tonal" color="red" @click="this.delete"> Delete </v-btn>
                    </div>
                </div>

                <div v-if="isJoined">
                    <my-share-buttons></my-share-buttons>
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
        },
        isAdmin: {
            type: Boolean,
            required: true,
        }
    },

    computed: {
        formatedDateTime() {
            return this.$store.getters['conference/formatedDateTime'](this.conference.id)
        },
    },

    methods: {
        delete() {
            this.$store.dispatch('conference/deleteConference', this.conference.id)
        },
    },
};
</script>

<style scoped>
    .v-card:not(.on-hover) {
        opacity: 0.8;
    }
</style>
