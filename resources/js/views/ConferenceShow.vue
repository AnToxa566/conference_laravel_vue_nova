<template>
    <p class="text-h4 mb-4">Details</p>

    <my-info-card>
        <template v-slot:header> Topic </template>
        <template v-slot:body> {{ this.conference.title }} </template>
    </my-info-card>

    <my-info-card>
        <template v-slot:header> Date / Time </template>
        <template v-slot:body> {{ this.formatedDateTime }} </template>
    </my-info-card>

    <my-info-card>
        <template v-slot:header> Address </template>
        <template v-slot:body> TODO </template>
    </my-info-card>

    <my-map></my-map>

    <my-info-card>
        <template v-slot:header> Country </template>
        <template v-slot:body> {{ this.conference.country }} </template>
    </my-info-card>

    <div class="d-flex justify-space-between">
        <div>
            <v-btn variant="tonal" color="white" class="me-2" @click="$router.push(`/`)"> Back </v-btn>

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
import MyJoinCancelButtons from '../components/UI/MyJoinCancelButtons.vue'
import MyShareButtons from '../components/UI/MyShareButtons.vue'
import MyInfoCard from '../components/UI/MyInfoCard.vue'
import MyMap from '../components/UI/MyMap.vue'

export default {
    components: {
        MyJoinCancelButtons,
        MyShareButtons,
        MyInfoCard,
        MyMap,
    },

    data() {
        return {
            id: null,
        };
    },

    computed: {
        conference() {
            return this.$store.getters['conference/conference']
        },
        formatedDateTime() {
            return this.$store.getters['conference/formatedDateTime'](this.id)
        },

        joinedConferencesId() {
            return (this.$store.getters['user_conferences/joinedConferencesId'])
        },
        isJoined() {
            return this.joinedConferencesId.includes(this.id)
        },

        isAdmin() {
            return this.$store.getters['auth/user'].type === 'admin'
        },
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
