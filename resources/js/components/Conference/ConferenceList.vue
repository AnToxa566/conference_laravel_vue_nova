<template>
    <div
        v-if="this.filteredConferences.length"
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
import pagination from '../../config/pagination'

export default {
    components: {
        ConferenceItem,
    },

    data: () => ({
        page: 1,
        perPage: pagination.PER_PAGE,
    }),

    created() {
        this.$store.dispatch('conference/fetchPaginatedConferences', {
            page: this.page,
            perPage: this.perPage,
        })
    },

    computed: {
        isAuthenticated() {
            return this.$store.getters['auth/authenticated']
        },

        filteredConferences() {
            return this.$store.getters['conference/filteredConferences']
        },

        conferencesPaginatedData() {
            return this.$store.getters['conference/conferencesPaginatedData']
        },
    },

    methods: {
        getResults(event) {
            this.page = event

            this.$store.dispatch('conference/fetchPaginatedConferences', {
                page: this.page,
                perPage: this.perPage,
            })
        },
    },
}
</script>
