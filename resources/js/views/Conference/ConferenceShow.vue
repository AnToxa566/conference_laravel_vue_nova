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
        <template v-slot:body> {{ this.formatedDateTime }} </template>
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
            <conference-delete-button
                v-if="isAdmin"
                :conferenceId="this.id"
            ></conference-delete-button>

            <join-cancel-buttons
                v-else
                :isJoined="this.isJoined"
                :conferenceId="this.id"
            ></join-cancel-buttons>
        </div>

        <!-- Append buttons -->

        <div>
            <share-buttons v-if="isJoined"></share-buttons>

            <export-button
                v-if="isAdmin"
                @startExport="exportMembers"
            >
                <template v-slot:title> Export members </template>
            </export-button>
        </div>
    </div>
</template>


<script>
import ConferenceDeleteButton from '../../components/Conference/ConferenceDeleteButton.vue';

export default {
    components: {
        ConferenceDeleteButton
    },

    data: () => ({
        id: null,
        confirmationDialog: false,
    }),

    created() {
        this.id = parseInt(this.$route.params.id, 10);
        this.$store.dispatch('conference/fetchDetailConference', this.id)
    },

    computed: {
        conference() {
            return this.$store.getters['conference/conference']
        },
        category() {
            return this.$store.getters['category/categoryById'](this.conference.category_id)
        },

        formatedDateTime() {
            return this.$store.getters['conference/formatedDateTime'](this.id)
        },

        joinedConferencesId() {
            return this.$store.getters['user_conferences/joinedConferencesId']
        },

        addressPosition() {
            return this.$store.getters['conference/addressPosition']
        },

        isJoined() {
            return this.joinedConferencesId.includes(this.id)
        },
        isAdmin() {
            return this.$store.getters['auth/isAdmin']
        },
        isHasAddress() {
            return this.addressPosition?.lat !== null && this.addressPosition?.lng !== null
        }
    },

    methods: {
        exportMembers() {
            this.$store.dispatch('conference/exportListeners', this.id)
        },

        delete(event) {
            if (event) {
                this.$store.dispatch('conference/deleteConference', this.id)
            }
        },
    },
}
</script>
