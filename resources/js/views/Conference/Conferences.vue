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
import { mapGetters } from 'vuex'

export default {
    components: {
        ConferenceList,
    },

    computed: {
        ...mapGetters({
            isAuthenticated: 'auth/authenticated',
            error: 'conference/error',
        }),
    },
}
</script>
