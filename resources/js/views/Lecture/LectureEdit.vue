<template>
    <my-header
        class="mb-4"
    >
        <template v-slot:header>
            {{ this.isUserOwnThisLecture ? 'Update lecture ' : this.$router.push({ name: '404' }) }}
        </template>
    </my-header>


    <lecture-form
        :conference="this.conferenceById"
        :lectureToEdit="this.lecture"
        @submit="updateLecture"
    >
        <template v-slot:extraButtons>
            <v-btn variant="text" color="white" class="mx-1" @click="this.cancelParticipation()"> Сancel participation </v-btn>
            <v-btn variant="tonal" color="white" class="mx-1" @click="$router.go(-1)"> Back </v-btn>
        </template>
    </lecture-form>
</template>


<script>
import LectureForm from '../../components/Lecture/LectureForm.vue'

export default {
    components: {
        LectureForm,
    },

    data() {
        return {
            conferenceId: null,
            lectureId: null,
        };
    },

    created() {
        this.conferenceId = parseInt(this.$route.params.conference_id, 10);
        this.lectureId = parseInt(this.$route.params.lecture_id, 10);

        this.$store.dispatch('lecture/fetchLectureById', this.lectureId)
    },

    computed: {
        conferenceById() {
            return this.$store.getters['conference/conferenceById'](this.conferenceId)
        },

        lecture() {
            return this.$store.getters['lecture/lecture']
        },
        userId() {
            return this.$store.getters['auth/user'].id
        },

        isUserOwnThisLecture() {
            return this.$store.getters['lecture/isUserOwnThisLecture']
        },
    },

    methods: {
        cancelParticipation() {
            this.$store.dispatch('lecture/deleteLecture', this.lectureId)
        },

        async updateLecture(lecture) {
            lecture.id = this.lectureId
            this.$store.dispatch('lecture/updateLecture', lecture)
        },
    },
}
</script>


<style scoped>

</style>
