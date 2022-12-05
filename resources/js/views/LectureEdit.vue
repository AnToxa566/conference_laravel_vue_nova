<template>
    <my-header
        class="mb-4"
    >
        <template v-slot:header>
            {{ this.isUserOwnThisLecture ? 'Update lecture ' : this.$router.push({ name: '404' }) }}
        </template>
    </my-header>


    <lecture-form
        :conferenceId="this.conference_id"
        :lectureToEdit="this.lecture"
        @submit="updateLecture"
    >
        <template v-slot:extraButtons>
            <v-btn variant="tonal" color="white" class="me-2" @click="$router.go(-1)"> Back </v-btn>
            <v-btn variant="tonal" color="white" class="mx-1" @click="this.cancelParticipation()"> Сancel participation </v-btn>
        </template>
    </lecture-form>
</template>


<script>
import LectureForm from '../components/LectureForm.vue'

export default {
    components: {
        LectureForm,
    },

    data() {
        return {
            conference_id: null,
            lecture_id: null,
        };
    },

    created() {
        this.conference_id = parseInt(this.$route.params.conference_id, 10);
        this.lecture_id = parseInt(this.$route.params.lecture_id, 10);

        this.$store.dispatch('lecture/fetchLectureById', this.lecture_id)
    },

    computed: {
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
            this.$store.dispatch('lecture/cancelParticipation', this.conference_id)
        },

        async updateLecture(lecture) {
            lecture.id = this.lecture_id
            this.$store.dispatch('lecture/updateLecture', lecture)
        },
    },
}
</script>


<style scoped>

</style>
