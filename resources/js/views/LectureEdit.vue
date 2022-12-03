<template>
    <my-header>
        <template v-slot:header>
            {{ this.isUserOwnThisLecture ? 'Update lecture ' : this.$router.push({ name: '404' }) }}
        </template>
    </my-header>
</template>


<script>
export default {
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
}
</script>


<style scoped>

</style>
