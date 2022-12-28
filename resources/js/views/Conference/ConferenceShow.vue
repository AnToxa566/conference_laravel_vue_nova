<template>
    <my-header>
        <template v-slot:header>Conference details</template>
    </my-header>

    <my-info-card>
        <template v-slot:header> Topic </template>
        <template v-slot:body> {{ this.conference.title }} </template>
    </my-info-card>

    <my-info-card>
        <template v-slot:header> Date / Time </template>
        <template v-slot:body> {{ this.formatedDateTime }} </template>
    </my-info-card>

    <my-info-card v-if="isHasAddress">
        <template v-slot:header> Address </template>
        <template v-slot:body> {{ this.formatedAddress }} </template>
    </my-info-card>

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

    <my-info-card>
        <template v-slot:header> Country </template>
        <template v-slot:body> {{ this.conference.country }} </template>
    </my-info-card>

    <my-info-card
        v-if="this.category"
    >
        <template v-slot:header> Category </template>
        <template v-slot:body> {{ this.category.title }} </template>
    </my-info-card>

    <div class="d-flex justify-space-between">
        <div>
            <v-btn variant="tonal" color="white" class="me-2" @click="$router.go(-1)"> Back </v-btn>

            <my-join-cancel-buttons
                v-if="!isAdmin"
                :isJoined="this.isJoined"
                :conferenceId="this.id"
            ></my-join-cancel-buttons>
            <v-btn v-else variant="tonal" color="red" @click="this.delete"> Delete </v-btn>
        </div>

        <div v-if="isJoined">
            <my-share-buttons></my-share-buttons>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            id: null,
        };
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
        formatedAddress() {
            return this.$store.getters['conference/formatedAddress']
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

    created() {
        this.id = parseInt(this.$route.params.id, 10);
        this.$store.dispatch('conference/fetchDetailConference', this.id)
    },

    methods: {
        delete() {
            this.$store.dispatch('conference/deleteConference', this.id)
        },
    },
}
</script>

<style scoped>

</style>