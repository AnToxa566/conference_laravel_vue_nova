<template>
    <conference-item
        v-for="conference in conferencesPaginatedDate.paginated_conferences"
        :key="conference.id"
        :conference="conference"
        :isJoined="joinedConferencesId.includes(parseInt(conference.id, 10))"
        :isAdmin="user.type === 'admin'"
    >
    </conference-item>

    <div class="text-center">
        <v-pagination
            v-model="page"
            :length="conferencesPaginatedDate.total_pages"
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

        conferencesPaginatedDate() {
            return this.$store.getters['conference/conferencesPaginatedDate']
        },

        joinedConferencesId() {
            return this.$store.getters['user_conferences/joinedConferencesId']
        },
    },

    mounted() {
        this.$store.dispatch('conference/fetchPaginatedConferences', this.page)

        if (this.authenticated) {
            this.$store.dispatch('user_conferences/fetchJoinedConferences', this.user.id)
        }
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
