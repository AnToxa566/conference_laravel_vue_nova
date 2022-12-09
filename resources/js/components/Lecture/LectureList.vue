<template>
    <lecture-item
        v-for="lecture in this.lectures"
        :key="lecture.id"
        :lecture="lecture"
    >
    </lecture-item>

    <div
        v-if="!this.lectures.length"
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
        lectures: {
            type: Array,
            required: true,
            default: [],
        },
    },
}
</script>
