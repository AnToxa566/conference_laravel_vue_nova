<template>
    <conference-item
        v-for="conference in conferences"
        :key="conference.id"
        :conference="conference"
        :isJoined="joinedConferencesId.includes(conference.id)"
    >
    </conference-item>

    <div class="text-center">
        <v-pagination
            v-model="page"
            :length="15"
            :total-visible="7"
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

        conferences() {
            return this.$store.getters['conference/conferences']
        },
        conferencesPaginatedDate() {
            return this.$store.getters['conference/conferencesPaginatedDate']
        },

        joinedConferencesId() {
            return this.$store.getters['user_conferences/joinedConferencesId']
        },
    },

    mounted() {
        this.$store.dispatch('conference/fetchAllConferences', this.page)

        if (this.authenticated) {
            this.$store.dispatch('user_conferences/fetchJoinedConferences', this.user.id)
        }
    },

    methods: {
        getResults() {
            this.$store.dispatch('conference/fetchAllConferences', this.page)
        },
    },
}
</script>

<style scoped>

</style>
