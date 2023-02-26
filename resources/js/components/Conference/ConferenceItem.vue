<template>
    <div class="mb-4">
        <v-hover
            v-slot="{ isHovering, props }"
            open-delay="200"
        >
            <v-card
                :elevation="isHovering ? 18 : 6"
                :class="{ 'on-hover': isHovering }"
                class="pa-4"
                variant="tonal"
                v-bind="props"
            >
                <!-- Title -->
                <v-card-title class="text-h5">
                    <div class="d-flex align-center justify-content-between">
                        {{ conference.title }}

                        <v-chip
                            v-if="this.category"
                            variant="outlined"
                            color="white"
                            :rounded="true"
                        >
                            #{{ this.category.title }}
                        </v-chip>
                    </div>
                </v-card-title>

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
                            :isJoined="this.isJoined"
                            :conference="this.conference"
                        ></join-cancel-buttons>
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
                    </div>
                </v-card-actions>
            </v-card>
        </v-hover>
    </div>
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
