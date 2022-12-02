<template>
    <div
        class="py-3 text-h4 font-weight-bold"
    >
        Lectures
    </div>

    <lecture-list
        :conference_id="this.conference_id"
    ></lecture-list>
</template>


<script>
import LectureList from '../components/LectureList.vue'

export default {
    components: {
        LectureList,
    },

    data() {
        return {
            conference_id: null,
        };
    },

    created() {
        this.conference_id = parseInt(this.$route.params.conference_id, 10);
    },
}
</script>


<style scoped>

</style>
