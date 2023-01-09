<template>
    <div class="d-flex mb-4">
        <v-card
            class="pa-4 w-100"
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

            <v-card-subtitle
                v-if="lectureStatus.content === WAITING"
            >
                Remains:
                <custom-timer
                    :toTimestamp="new Date(this.lecture.date_time_start).getTime()"
                    class="font-weight-bold"
                ></custom-timer>
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

        <v-badge
            :content="lectureStatus.content"
            :color="lectureStatus.color"
            text-color="white"
            location="bottom"
        ></v-badge>
    </div>
</template>


<script>
import moment from 'moment'

export default {
    data: () => ({
        show: false,

        WAITING: 'Waiting',
        STARTED: 'Started',
        ENDED: 'Ended',

        WAITING_COLOR: 'yellow-darken-2',
        STARTED_COLOR: 'green-darken-2',
        ENDED_COLOR: 'red-darken-2',
    }),

    props: {
        lecture: {
            type: Object,
            required: true,
        },
    },

    computed: {
        startFormattedTime() {
            return moment(new Date(this.lecture.date_time_start)).format('HH:mm')
        },
        endFormattedTime() {
            return moment(new Date(this.lecture.date_time_end)).format('HH:mm')
        },

        lectureStatus() {
            if (Date.now() > new Date(this.lecture.date_time_end).getTime()) {
                return {
                    content: this.ENDED,
                    color: this.ENDED_COLOR,
                }
            }

            if (Date.now() > new Date(this.lecture.date_time_start).getTime()) {
                return {
                    content: this.STARTED,
                    color: this.STARTED_COLOR,
                }
            }

            return {
                content: this.WAITING,
                color: this.WAITING_COLOR,
            }
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
