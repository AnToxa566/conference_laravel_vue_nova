<template>
    <conference-item
        v-for="conference in conferencesPaginatedData.paginatedConferences"
        :key="conference.id"
        :conference="conference"
    >
    </conference-item>

    <div
        v-if="!this.conferencesPaginatedData.paginatedConferences.length"
        class="text-subtitle-1 font-weight-medium text-center"
    >
        Conferences not found
    </div>

    <div v-else class="text-center">
        <v-pagination
            v-model="page"
            :length="conferencesPaginatedData.totalPages"
            @update:modelValue="getResults"
        ></v-pagination>
    </div>
</template>

<script>
import { defineAsyncComponent } from 'vue'

export default {
    data: () => ({
        page: 1,
        perPage: 15,
    }),

    components: {
        ConferenceItem: defineAsyncComponent(() => import('./ConferenceItem.vue'))
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
