<template>
    <div class="d-flex mb-4">
        <v-card
            class="pa-4 w-100"
        >

            <!-- Lecture's Title -->

            <v-card-title
                style="cursor: pointer;"
                @click="$router.push(`/conferences/${lecture.conference_id}/lectures/${lecture.id}`)"
            >
                <span class="font-weight-bold text-h5">{{ lecture.title }}</span>
            </v-card-title>

            <!-- Lecture's Time -->

            <v-card-subtitle>
                Time:
                from <span class="font-weight-bold">{{ this.startFormattedTime }}</span>
                to <span class="font-weight-bold">{{ this.endFormattedTime }}</span>
            </v-card-subtitle>

            <!-- Timer until the start of the Lecture -->

            <v-card-subtitle
                v-if="this.lectureStatusText === this.waiting"
            >
                Remains:
                <custom-timer
                    class="font-weight-bold"
                    :toTimestamp="new Date(this.lecture.date_time_start).getTime()"
                    @timerStoped="lectureStarted"
                ></custom-timer>
            </v-card-subtitle>

            <!-- Invisible timer until the end of the Lecture -->

            <custom-timer
                v-if="this.lectureStatusText === this.started"
                :visible="false"
                :toTimestamp="new Date(this.lecture.date_time_end).getTime()"
                @timerStoped="lectureEnded"
            ></custom-timer>

            <!-- Lecture's Description -->

            <v-card-text>
                <span class="text-body-2">Description: {{ !this.isTextOverlay || show ? lecture.description : this.slicedDescription }}</span>
            </v-card-text>

            <!-- More / Less Buttons -->

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
                <!-- Comments Count -->
                <v-card-subtitle>
                    <span class="text-caption">{{ this.lecture.comments_count + ' comments' }}</span>
                </v-card-subtitle>

                <!-- Add / Remove Favorite Button -->
                <favorite-btn
                    :isFavourite="this.isFavourite"
                    @add="addLectureToFavorites"
                    @remove="removeLectureFromFavorites"
                >
                </favorite-btn>
            </div>
        </v-card>

        <!-- Lecture Card's Badge -->

        <v-badge
            :content="lectureStatusText"
            :color="lectureStatusColor"
            text-color="white"
            location="bottom"
        ></v-badge>
    </div>
</template>


<script>
import lectureConsts from '../../config/lecture'
import moment from 'moment'

export default {
    data: () => ({
        show: false,

        lectureStatusText: '',
        lectureStatusColor: '',

        waiting: lectureConsts.WAITING,
        started: lectureConsts.STARTED,
        ended: lectureConsts.ENDED,
    }),

    props: {
        lecture: {
            type: Object,
            required: true,
        },
    },

    mounted() {
        if (this.isLectureEnded) {
            this.lectureStatusText = lectureConsts.ENDED
            this.lectureStatusColor = lectureConsts.ENDED_COLOR
        }

        if (this.isLectureStarted) {
            this.lectureStatusText = lectureConsts.STARTED
            this.lectureStatusColor = lectureConsts.STARTED_COLOR
        }

        if (this.isLectureWaiting) {
            this.lectureStatusText = lectureConsts.WAITING
            this.lectureStatusColor = lectureConsts.WAITING_COLOR
        }
    },

    computed: {
        startFormattedTime() {
            return moment(new Date(this.lecture.date_time_start)).format('HH:mm')
        },
        endFormattedTime() {
            return moment(new Date(this.lecture.date_time_end)).format('HH:mm')
        },

        isLectureWaiting() {
            return Date.now() < new Date(this.lecture.date_time_start).getTime()
        },
        isLectureStarted() {
            return Date.now() > new Date(this.lecture.date_time_start).getTime() && Date.now() < new Date(this.lecture.date_time_end).getTime()
        },
        isLectureEnded() {
            return Date.now() > new Date(this.lecture.date_time_end).getTime()
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

        lectureStarted() {
            this.lectureStatusText = lectureConsts.STARTED
            this.lectureStatusColor = lectureConsts.STARTED_COLOR
        },

        lectureEnded() {
            this.lectureStatusText = lectureConsts.ENDED
            this.lectureStatusColor = lectureConsts.ENDED_COLOR
        },
    },
}
</script>
