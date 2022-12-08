<template>
    <v-card
        class="pa-4 mb-4"
    >
        <v-card-title
            style="cursor: pointer;"
            @click="$router.push(`/conferences/${lecture.conference_id}/lectures/${lecture.id}`)"
        >
            <span class="font-weight-bold text-h5">{{ lecture.title }}</span>
        </v-card-title>

        <v-card-subtitle>
            Time:
            from <span class="font-weight-bold">{{ this.startFormattedTime }}</span>
            to <span class="font-weight-bold">{{ this.endFormattedTime }}</span>
        </v-card-subtitle>

        <v-card-text>
            <span class="text-body-2">Description: {{ !this.isTextOverlay || show ? lecture.description : this.slicedDescription }}</span>
        </v-card-text>

        <v-card-actions
            v-if="this.isTextOverlay"
        >
            <v-spacer></v-spacer>

            <v-btn
                variant="text" color="white"
                @click="show = !show"
            >
                {{ !show ? 'More' : 'Less' }}
            </v-btn>
        </v-card-actions>

        <div class="d-flex justify-space-between align-center">
            <v-card-subtitle>
                <span class="text-caption">{{ commentsCount + ' comments' }}</span>
            </v-card-subtitle>

            <custom-favorites
                :isFavourite="this.isFavourite"
                @add="addLectureToFavorites"
                @remove="removeLectureFromFavorites"
            >
            </custom-favorites>
        </div>
    </v-card>
</template>


<script>
import moment from 'moment'

export default {
    data: () => ({
        show: false,
    }),

    props: {
        lecture: {
            type: Object,
            required: true,
        },
    },

    computed: {
        startFormattedTime() {
            const format = 'HH:mm'
            const date = new Date(this.lecture.date_time_start)

            return moment(date).format(format)
        },
        endFormattedTime() {
            const format = 'HH:mm'
            const date = new Date(this.lecture.date_time_end)

            return moment(date).format(format)
        },

        isTextOverlay() {
            return this.lecture.description.length > 100
        },
        slicedDescription() {
            return this.lecture.description.slice(0, 100) + '...'
        },

        commentsCount() {
            return this.$store.getters['lecture/commentsCounts'].find(counts => parseInt(counts.lecture_id, 10) === this.lecture.id).comments_count
        },

        isFavourite() {
            return this.$store.getters['favorite/isLectureFavorited'](this.lecture.id)
        },
    },

    methods: {
        addLectureToFavorites() {
            console.log('add')

            this.$store.dispatch('favorite/addLectureToFavorite', this.lecture.id)
        },

        removeLectureFromFavorites() {
            console.log('remove')

            this.$store.dispatch('favorite/removeLectureFromFavorite', this.lecture.id)
        },
    },
}
</script>


<style scoped>

</style>
