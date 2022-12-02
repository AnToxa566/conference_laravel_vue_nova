<template>
    <my-header>
        <template v-slot:header>Lectures</template>
    </my-header>

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
