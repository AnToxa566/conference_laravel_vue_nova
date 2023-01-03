<template>

    <!-- Header -->

    <my-header>
        <template v-slot:header>Lectures</template>
    </my-header>

    <!-- Error alert -->

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

            <lecture-filter-navigation
                :conferenceId="this.conferenceId"
            ></lecture-filter-navigation>

            <!-- Export Button -->

            <export-button
                v-if="isAdmin"
                class="mt-1"
                @startExport="exportLectures"
            >
                <template v-slot:title> Export lectures </template>
            </export-button>
        </div>

        <!-- List of Lectures -->

        <div class="w-100">
            <lecture-list
                v-if="this.filteredLectures.length"
                :lectures="this.filteredLectures"
            ></lecture-list>

            <lecture-item-skeleton
                v-else
            ></lecture-item-skeleton>
        </div>
    </div>
</template>


<script>
import LectureList from '../../components/Lecture/LectureList.vue'

export default {
    components: {
        LectureList,
    },

    data: () => ({
        conferenceId: null,
    }),

    created() {
        this.conferenceId = parseInt(this.$route.params.id, 10);
    },

    computed: {
        isAuthenticated() {
            return this.$store.getters['auth/authenticated']
        },

        isAdmin() {
            return this.$store.getters['auth/isAdmin']
        },

        filteredLectures() {
            return this.$store.getters['lecture/filteredLectures']
        },

        error() {
            return this.$store.getters['lecture/error']
        },
    },

    methods: {
        exportLectures() {
            this.$store.dispatch('lecture/exportLectures', this.conferenceId)
        },
    }
}
</script>
