<template>
    <my-header>
        <template v-slot:header>Lectures</template>
    </my-header>

    <lecture-list
        :lectures="this.lecturesOfConference"
    ></lecture-list>
</template>


<script>
import LectureList from '../components/LectureList.vue'

export default {
    components: {
        LectureList,
    },

    data: () => ({
        conferenceId: null,
    }),

    created() {
        this.conferenceId = parseInt(this.$route.params.conference_id, 10);
    },

    computed: {
        lecturesOfConference() {
            return this.$store.getters['lecture/lectures'].filter(lecture => parseInt(lecture.conference_id, 10) === this.conferenceId)
        },
    },
}
</script>


<style scoped>

</style>
