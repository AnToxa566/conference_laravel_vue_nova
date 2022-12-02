<template>
    <lecture-item
        v-for="lecture in lecturesOfConference"
        :key="lecture.id"
        :lecture="lecture"
    >
    </lecture-item>

    <div
        v-if="!lecturesOfConference.length"
        class="text-h6 font-weight-medium text-center"
    >
        Lectures not added yet :(
    </div>
</template>


<script>
import LectureItem from './LectureItem.vue'

export default {
    components: {
        LectureItem,
    },

    props: {
        conference_id: {
            type: Number,
            required: true,
        },
    },

    computed: {
        lecturesOfConference() {
            return this.$store.getters['lecture/lectures'].filter(lecture => {
                return lecture.conference_id === this.conference_id
            })
        },
    },
}
</script>


<style scoped>

</style>
