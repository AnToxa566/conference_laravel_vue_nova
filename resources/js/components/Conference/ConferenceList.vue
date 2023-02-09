<template>
    <div
        v-if="this.conferences.length"
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

import { mapGetters } from 'vuex'

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
        ...mapGetters({
            isAuthenticated: 'auth/authenticated',

            allConferences: 'conference/conferences',
            filteredConferences: 'conference/filteredConferences',
            conferencesPaginatedData: 'conference/conferencesPaginatedData',
        }),

        conferences() {
            return this.isAuthenticated ? this.filteredConferences : this.allConferences
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
