<template>
    <div
        v-if="this.filteredConferences.length !== 0"
    >
        <conference-item
            v-for="conference in conferencesPaginatedData.paginatedConferences"
            :key="conference.id"
            :conference="conference"
        >
        </conference-item>

        <v-pagination
            v-model="page"
            :length="conferencesPaginatedData.totalPages"
            @update:modelValue="getResults"
        ></v-pagination>
    </div>

    <conference-item-skeleton
        v-else
    ></conference-item-skeleton>
</template>

<script>
import ConferenceItem from './ConferenceItem.vue'

export default {
    data: () => ({
        page: 1,
        perPage: 15,
    }),

    components: {
        ConferenceItem,
    },

    computed: {
        isAuthenticated() {
            return this.$store.getters['auth/authenticated']
        },

        conferences() {
            return this.$store.getters['conference/conferences']
        },
        filteredConferences() {
            return this.$store.getters['conference/filteredConferences']
        },

        conferencesPaginatedData() {
            return this.$store.getters['conference/conferencesPaginatedData']
        },
    },

    created() {
        this.$store.dispatch('conference/fetchPaginatedConferences', {
            page: this.page,
            perPage: this.perPage,
            conferences: this.isAuthenticated ? this.filteredConferences : this.conferences,
        })
    },

    methods: {
        getResults(event) {
            this.page = event

            this.$store.dispatch('conference/fetchPaginatedConferences', {
                page: this.page,
                perPage: this.perPage,
                conferences: this.isAuthenticated ? this.filteredConferences : this.conferences,
            })
        },
    },
}
</script>
