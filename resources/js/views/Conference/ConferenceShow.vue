<template>

    <!-- Header -->

    <custom-header>
        <template v-slot:header>Conference details</template>
    </custom-header>

    <!-- Title -->

    <info-card>
        <template v-slot:header> Topic </template>
        <template v-slot:body> {{ this.conference.title }} </template>
    </info-card>

    <!-- Date & Time -->

    <info-card>
        <template v-slot:header> Date / Time </template>
        <template v-slot:body> {{ this.formatedDateTime(this.id) }} </template>
    </info-card>

    <!-- Address -->

    <info-card v-if="isHasAddress">
        <template v-slot:header> Address </template>
        <template v-slot:body> {{ this.conference.address }} </template>
    </info-card>

    <!-- Google Map -->

    <v-card v-if="isHasAddress" class="mb-4">
        <GMapMap
            :center="addressPosition"
            :zoom="16"
            :options="{
                zoomControl: false,
                scaleControl: false,
                streetViewControl: false,
            }"
            map-type-id="roadmap"
            class="w-100"
            style="height: 500px"
        >
            <GMapMarker
                :position="addressPosition"
                :clickable="true"
                :draggable="false"
            />
        </GMapMap>
    </v-card>

    <!-- Country -->

    <info-card>
        <template v-slot:header> Country </template>
        <template v-slot:body> {{ this.conference.country }} </template>
    </info-card>

    <!-- Category -->

    <info-card
        v-if="this.category"
    >
        <template v-slot:header> Category </template>
        <template v-slot:body> {{ this.category.title }} </template>
    </info-card>

    <!-- Buttons -->

    <div class="d-flex justify-space-between align-center">

        <!-- Preppend buttons -->

        <div>
            <join-cancel-buttons
                :isJoined="this.isJoined"
                :conference="this.conference"
            ></join-cancel-buttons>
        </div>

        <!-- Append buttons -->

        <div>
            <share-buttons v-if="isJoined"></share-buttons>
        </div>
    </div>
</template>


<script>
import { mapGetters } from 'vuex'

export default {
    data: () => ({
        id: null,
    }),

    created() {
        this.id = parseInt(this.$route.params.id, 10);
        this.$store.dispatch('conference/fetchDetailConference', this.id)
    },

    computed: {
        ...mapGetters({
            conference: 'conference/conference',
            categoryById: 'category/categoryById',
            formatedDateTime: 'conference/formatedDateTime',
            joinedConferencesId: 'user_conferences/joinedConferencesId',
        }),

        category() {
            return this.categoryById(this.conference.category_id)
        },

        addressPosition() {
            return {
                lat: this.conference.latitude,
                lng: this.conference.longitude,
            }
        },

        isJoined() {
            return this.joinedConferencesId.includes(this.id)
        },
        isHasAddress() {
            return this.addressPosition?.lat !== null && this.addressPosition?.lng !== null
        }
    },
}
</script>
