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
                            v-if="this.conference.category_id"
                            variant="outlined"
                            color="white"
                            :rounded="true"
                        >
                            #{{ this.categoryById(this.conference.category_id).title }}
                        </v-chip>
                    </div>
                </v-card-title>

                <!-- Date & Time -->
                <v-card-subtitle> {{ this.formatedDateTime(this.conference.id) }} </v-card-subtitle>

                <!-- Country -->
                <v-card-subtitle> {{ this.conferenceCountry.name }} </v-card-subtitle>


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
import { mapGetters } from 'vuex'

export default {
    props: {
        conference: {
            type: Object,
            required: true,
        },
    },

    computed: {
        ...mapGetters({
            joinedConferencesId: 'user_conferences/joinedConferencesId',
            formatedDateTime: 'conference/formatedDateTime',
            categoryById: 'category/categoryById',
            countries: 'country/countries'
        }),

        isJoined() {
            return this.joinedConferencesId.includes(parseInt(this.conference.id, 10))
        },

        conferenceCountry() {
            return this.countries.find(c => c.short_code === this.conference.country)
        },
    },
};
</script>


<style scoped>
    .v-card:not(.on-hover) {
        opacity: 0.8;
    }
</style>
