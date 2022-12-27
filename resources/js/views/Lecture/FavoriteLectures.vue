<template>
    <my-header>
        <template v-slot:header>Favorite Lectures</template>
    </my-header>

    <lecture-list
        v-if="this.favoriteLectures.length !== 0"
        :lectures="this.favoriteLectures"
    ></lecture-list>

    <div
        v-else
        class="text-center text-h6"
    >
        No favorites lectures yet
    </div>
</template>


<script>
import LectureList from '../../components/Lecture/LectureList.vue'

export default {
    components: {
        LectureList,
    },

    computed: {
        favoriteLectures() {
            const lectures = this.$store.getters['lecture/lectures']
            const favoriteLecturesId = this.$store.getters['favorite/favoritedLecturesId']

            return lectures.filter(lecture => favoriteLecturesId.includes(lecture.id))
        },
    },
}
</script>


<style scoped>

</style>
