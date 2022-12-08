<template>
    <conference-item
        v-for="conference in conferencesPaginatedData.paginated_conferences"
        :key="conference.id"
        :conference="conference"
        :isJoined="joinedConferencesId.includes(parseInt(conference.id, 10))"
        :isAdmin="user.type === this.adminType"
    >
    </conference-item>

    <div class="text-center">
        <v-pagination
            v-model="page"
            :length="conferencesPaginatedData.total_pages"
            @update:modelValue="getResults"
        ></v-pagination>
    </div>
</template>

<script>
import ConferenceItem from './ConferenceItem.vue'

export default {
    data() {
        return {
            page: 1,
        }
    },

    components: {
        ConferenceItem,
    },

    computed: {
        authenticated() {
            return this.$store.getters['auth/authenticated']
        },
        user() {
            return this.$store.getters['auth/user']
        },
        adminType() {
            return this.$store.getters['auth/adminType']
        },

        conferencesPaginatedData() {
            return this.$store.getters['conference/conferencesPaginatedData']
        },

        joinedConferencesId() {
            return this.$store.getters['user_conferences/joinedConferencesId']
        },
    },

    created() {
        this.$store.dispatch('conference/fetchPaginatedConferences', this.page)
    },

    methods: {
        getResults(event) {
            this.page = event
            this.$store.dispatch('conference/fetchPaginatedConferences', this.page)
        },
    },
}
</script>

<style scoped>

</style>
