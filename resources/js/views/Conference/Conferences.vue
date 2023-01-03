<template>

    <!-- Header -->

    <my-header>
        <template v-slot:header>Conferences</template>
    </my-header>

    <!-- Error Alert -->

    <custom-error-alert
        :errorMessage='this.error'
        class="mb-6"
    >
    </custom-error-alert>

    <!-- Main Content -->

    <div class="d-flex">
        <div
            v-if="isAuthenticated"
            class="w-25 me-4"
        >
            <!-- Filter Navigation -->

            <conference-filter-navigation></conference-filter-navigation>

            <!-- Export Button -->

            <export-button
                v-if="isAdmin"
                class="mt-1"
                @startExport="exportConferences"
            >
                <template v-slot:title> Export conferences </template>
            </export-button>
        </div>

        <!-- Conferences List -->

        <div class="w-100">
            <conference-list></conference-list>
        </div>
    </div>
</template>


<script>
import ConferenceList from '../../components/Conference/ConferenceList.vue'

export default {
    components: {
        ConferenceList,
    },

    computed: {
        isAuthenticated() {
            return this.$store.getters['auth/authenticated']
        },

        isAdmin() {
            return this.$store.getters['auth/isAdmin']
        },

        error() {
            return this.$store.getters['conference/error']
        },
    },

    methods: {
        exportConferences() {
            this.$store.dispatch('conference/exportConferences')
        },
    }
}
</script>
