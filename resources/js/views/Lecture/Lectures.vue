<template>
    <my-header>
        <template v-slot:header>Lectures</template>
    </my-header>

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
                :lectures="this.filteredLectures"
            ></lecture-list>
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
    },
}
</script>
