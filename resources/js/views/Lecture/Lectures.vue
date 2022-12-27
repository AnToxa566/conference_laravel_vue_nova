<template>
    <my-header>
        <template v-slot:header>Lectures</template>
    </my-header>

    <custom-error-alert
        :errorMessage='this.error'
        class="mb-6"
    >
    </custom-error-alert>

    <div class="d-flex">
        <div
            v-if="isAuthenticated"
            class="w-25 me-4"
        >
            <lecture-filter-navigation
                :conferenceId="this.conferenceId"
            ></lecture-filter-navigation>
        </div>

        <div class="w-100">
            <lecture-list
                v-if="this.filteredLectures.length !== 0"
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

        filteredLectures() {
            return this.$store.getters['lecture/filteredLectures']
        },

        error() {
            return this.$store.getters['lecture/error']
        },
    },
}
</script>
