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

    <!-- Main Block -->

    <div class="d-flex">
        <div
            v-if="isAuthenticated"
            class="w-25 me-4"
        >
            <!-- Filter Navigation -->

            <conference-filter-navigation></conference-filter-navigation>

            <!-- Export Button -->

            <div
                v-if="isAdmin"
                class="mt-1"
            >
                <span
                    class="me-1"
                    style="cursor: pointer;"
                    @click="exportConferences"
                >
                    Export conferences
                </span>

                <v-progress-circular
                    v-if="exportLoading"
                    indeterminate
                    size="15"
                    width="3"
                ></v-progress-circular>
            </div>
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

    data: () => ({
        exportLoading: false,
    }),

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
            if (!this.exportLoading) {
                this.exportLoading = true

                this.$store.dispatch('conference/exportConferences')
            }
        },
    }
}
</script>
