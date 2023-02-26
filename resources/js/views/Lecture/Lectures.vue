<template>

    <!-- Header -->

    <custom-header>
        <template v-slot:header>Lectures</template>
    </custom-header>

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
import { mapGetters } from 'vuex'

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
        ...mapGetters({
            isAuthenticated: 'auth/authenticated',
            filteredLectures: 'lecture/filteredLectures',
            error: 'lecture/error',
        }),
    },
}
</script>
