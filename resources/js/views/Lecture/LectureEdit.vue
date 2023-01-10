<template>
    <custom-header
        class="mb-4"
    >
        <template v-slot:header>
            {{ this.isUserOwnThisLecture ? 'Update lecture ' : this.$router.push({ name: '404' }) }}
        </template>
    </custom-header>

    <custom-error-alert
        :errorMessage='this.error'
        class="mb-6"
    >
    </custom-error-alert>

    <lecture-form
        :conferenceId="this.conferenceId"
        :lectureToEdit="this.lecture"
        @submit="updateLecture"
    >
        <template v-slot:extraButtons>
            <join-cancel-buttons
                :isJoined="true"
                :conference="this.conferenceById"
            ></join-cancel-buttons>
        </template>
    </lecture-form>
</template>


<script>
import LectureForm from '../../components/Lecture/LectureForm.vue'

export default {
    components: {
        LectureForm,
    },

    data: () => ({
        conferenceId: null,
        lectureId: null,
    }),

    created() {
        this.conferenceId = parseInt(this.$route.params.conference_id, 10);
        this.lectureId = parseInt(this.$route.params.lecture_id, 10);

        this.$store.dispatch('lecture/fetchLectureById', this.lectureId)
        this.$store.commit('lecture/SET_ERROR', '')
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
            return this.$store.getters['lecture/isUserOwnThisLecture'](this.lectureId)
        },

        error() {
            return this.$store.getters['lecture/error']
        },
    },

    methods: {
        async updateLecture(lecture) {
            lecture.id = this.lectureId
            this.$store.dispatch('lecture/updateLecture', lecture)
        },
    },
}
</script>


<style scoped>

</style>
