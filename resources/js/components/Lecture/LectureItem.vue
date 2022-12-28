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
                <span class="text-caption">{{ this.lecture.comments_count + ' comments' }}</span>
            </v-card-subtitle>

            <favorite-btn
                :isFavourite="this.isFavourite"
                @add="addLectureToFavorites"
                @remove="removeLectureFromFavorites"
            >
            </favorite-btn>
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

        isFavourite() {
            return this.$store.getters['favorite/isLectureFavoritedById'](this.lecture.id)
        },
    },

    methods: {
        addLectureToFavorites() {
            this.$store.dispatch('favorite/addLectureToFavorite', this.lecture.id)
        },

        removeLectureFromFavorites() {
            this.$store.dispatch('favorite/removeLectureFromFavorite', this.lecture.id)
        },
    },
}
</script>