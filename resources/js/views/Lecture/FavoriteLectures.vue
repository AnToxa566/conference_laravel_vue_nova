<template>
    <custom-header>
        <template v-slot:header>Favorite Lectures</template>
    </custom-header>

    <lecture-list
        v-if="this.favoriteLectures.length"
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
import { mapGetters } from 'vuex'

export default {
    components: {
        LectureList,
    },

    computed: {
        ...mapGetters({
            lectures: 'lecture/lectures',
            favoritedLecturesId: 'favorite/favoritedLecturesId',
        }),

        favoriteLectures() {
            return this.lectures.filter(lecture => this.favoritedLecturesId.includes(lecture.id))
        },
    },
}
</script>
