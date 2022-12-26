<template>
    <lecture-item
        v-for="lecture in this.lectures"
        :key="lecture.id"
        :lecture="lecture"
    >
    </lecture-item>
</template>


<script>
import LectureItem from './LectureItem.vue'

export default {
    components: {
        LectureItem,
    },

    props: {
        lectures: {
            type: Array,
            required: true,
            default: [],
        },
    },
}
</script>
