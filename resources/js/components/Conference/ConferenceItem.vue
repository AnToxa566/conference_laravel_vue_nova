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
                        <v-btn variant="tonal" color="white" class="mx-1" @click="$router.push(`/conferences/${conference.id}/edit`)"> Update </v-btn>
                        <v-btn variant="text" color="red" class="mx-1" @click="this.delete"> Delete </v-btn>
                    </div>
                </div>

                <div
                    class="d-flex justify-content-end align-center"
                >
                    <div
                        v-if="isJoined"
                        class="mx-2"
                    >
                        <my-share-buttons></my-share-buttons>
                    </div>

                    <v-chip
                        v-if="this.category"
                        prepend-icon="mdi-tag-outline"
                        size="large"
                        class="mx-2"
                    >
                        {{ this.category.title }}
                    </v-chip>
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
    },

    computed: {
        isJoined() {
            return this.$store.getters['user_conferences/joinedConferencesId'].includes(parseInt(this.conference.id, 10))
        },
        isAdmin() {
            return this.$store.getters['auth/user'].type === this.$store.getters['auth/adminType']
        },

        formatedDateTime() {
            return this.$store.getters['conference/formatedDateTime'](this.conference.id)
        },

        category() {
            return this.$store.getters['category/categoryById'](this.conference.category_id)
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
