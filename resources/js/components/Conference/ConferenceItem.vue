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
            <!-- Title -->
            <v-card-title class="text-h5"> {{ conference.title }} </v-card-title>

            <!-- Date & Time -->
            <v-card-subtitle> {{ formatedDateTime }} </v-card-subtitle>

            <!-- Country -->
            <v-card-subtitle> {{ conference.country }} </v-card-subtitle>


            <!-- Buttons -->

            <v-card-actions class="d-flex justify-space-between">

                <!-- Prepend Buttons -->

                <div class="d-flex">
                    <!-- Details -->
                    <v-btn variant="tonal" color="white" class="mx-1" @click="$router.push(`/conferences/${conference.id}`)"> Details </v-btn>

                    <!-- Lectures -->
                    <v-btn variant="tonal" color="white" class="mx-1" @click="$router.push(`/conferences/${conference.id}/lectures`)"> Lectures </v-btn>

                    <!-- Join / Cancel -->
                    <join-cancel-buttons
                        v-if="!isAdmin"
                        :isJoined="this.isJoined"
                        :conference="this.conference"
                    ></join-cancel-buttons>

                    <div v-else>
                        <!-- Update -->
                        <v-btn variant="tonal" color="white" class="mx-1" @click="$router.push(`/conferences/${conference.id}/edit`)"> Update </v-btn>

                        <!-- Delete -->
                        <conference-delete-button :conferenceId="this.conference.id"></conference-delete-button>
                    </div>
                </div>

                <!-- Append Buttons -->

                <div
                    class="d-flex justify-content-end align-center"
                >

                    <!-- Share Buttons -->

                    <div
                        v-if="isJoined"
                        class="mx-2"
                    >
                        <share-buttons></share-buttons>
                    </div>

                    <!-- Category Chip -->

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
import ConferenceDeleteButton from '../Conference/ConferenceDeleteButton.vue';

export default {
    components: {
        ConferenceDeleteButton
    },

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
};
</script>


<style scoped>
    .v-card:not(.on-hover) {
        opacity: 0.8;
    }
</style>
