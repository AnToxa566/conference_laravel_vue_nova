<template>

    <!-- Header -->

    <custom-header>
        <template v-slot:header>Conferences</template>
    </custom-header>

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

        error() {
            return this.$store.getters['conference/error']
        },
    },
}
</script>
