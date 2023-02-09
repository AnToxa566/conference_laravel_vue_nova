<template>
    <v-expansion-panels
        v-model="panel"
        variant="accordion"
        multiple
    >

        <!-- Find lectures which starts in this time or after -->

        <filter-time
            v-model="filter.startTimeAfter"

            :minTime="{ hours: 0, minutes: 0, }"
            :maxTime="{ hours: 23, minutes: 59, }"

            @update:modelValue="updateFilters"
        >
            <template v-slot:title> Time, after </template>
        </filter-time>


        <!-- Find lectures which starts in this time or before -->

        <filter-time
            v-model="filter.startTimeBefore"

            :minTime="{ hours: 0, minutes: 0, }"
            :maxTime="{ hours: 23, minutes: 59, }"

            @update:modelValue="updateFilters"
        >
            <template v-slot:title> Time, before </template>
        </filter-time>


        <!-- Find lectures whose duration is included in this interval -->

        <filter-range-slider
            v-model="filter.lectureDurationRange"

            :min="minLectureDuration"
            :max="maxLectureDuration"

            @update:modelValue="updateFilters"
        >
            <template v-slot:title> Lecture duration, min </template>
        </filter-range-slider>


        <!-- Find lectures which contains any of these selected categories -->

        <filter-multiple-selects
            v-model="filter.selectedCategoriesId"
            :selectItems="categories"

            @update:modelValue="updateFilters"
        >
            <template v-slot:title> Category </template>
        </filter-multiple-selects>
    </v-expansion-panels>


    <!-- Reset all filters button -->

    <div
        class="text-red-darken-1 mt-4"
        style="cursor: pointer;"
        @click="reserFilters"
    >
        Reset filters
    </div>
</template>


<script>
import lecture from '../../config/lecture'

import { mapGetters } from 'vuex'

export default {
    data: () => ({
        filter: {
            startTimeAfter: null,
            startTimeBefore: null,
            lectureDurationRange: [lecture.MIN_LECTURE_DURATION, lecture.MAX_LECTURE_DURATION],
            selectedCategoriesId: [],
        },

        minLectureDuration: lecture.MIN_LECTURE_DURATION,
        maxLectureDuration: lecture.MAX_LECTURE_DURATION,

        panel: [],

        timeout: null,
    }),

    props: {
        conferenceId: {
            type: Number,
            required: true,
        },
    },

    created() {
        this.updateFilters()
    },

    computed: {
        ...mapGetters({
            categories: 'category/categories',
        }),
    },

    methods: {
        updateFilters() {
            clearTimeout(this.timeout)

            this.$store.commit('lecture/storeFilteredLectures', [])

            const self = this
            this.timeout = setTimeout(() => self.fetchFilteredLectures(), 800);
        },

        fetchFilteredLectures() {
            let afterTimeHours = this.filter.startTimeAfter ? this.filter.startTimeAfter.hours : null
            let afterTimeMinutes = this.filter.startTimeAfter ? this.filter.startTimeAfter.minutes : null

            let beforeTimeHours = this.filter.startTimeBefore ? this.filter.startTimeBefore.hours : null
            let beforeTimeMinutes = this.filter.startTimeBefore ? this.filter.startTimeBefore.minutes : null

            afterTimeHours = afterTimeHours != null && afterTimeHours < 10 ? '0' + afterTimeHours : afterTimeHours
            afterTimeMinutes = afterTimeMinutes != null && afterTimeMinutes < 10 ? '0' + afterTimeMinutes : afterTimeMinutes

            beforeTimeHours = beforeTimeHours != null && beforeTimeHours < 10 ? '0' + beforeTimeHours : beforeTimeHours
            beforeTimeMinutes = beforeTimeMinutes != null && beforeTimeMinutes < 10 ? '0' + beforeTimeMinutes : beforeTimeMinutes

            this.$store.dispatch('lecture/fetchFilteredLectures', {
                conferenceId: this.conferenceId,

                minDuration: this.filter.lectureDurationRange[0],
                maxDuration: this.filter.lectureDurationRange[1],

                startTimeAfter: this.filter.startTimeAfter ? afterTimeHours + ':' + afterTimeMinutes + ':' + '00' : this.filter.startTimeAfter,
                startTimeBefore: this.filter.startTimeBefore ? beforeTimeHours + ':' + beforeTimeMinutes + ':' + '00' : this.filter.startTimeBefore,

                categoriesId: this.filter.selectedCategoriesId,
            })
        },

        reserFilters() {
            this.filter.startTimeAfter = null
            this.filter.startTimeBefore = null
            this.filter.lectureDurationRange = [lecture.MIN_LECTURE_DURATION, lecture.MAX_LECTURE_DURATION]
            this.filter.selectedCategoriesId = []

            this.panel = []

            this.updateFilters()
        },
    },
}
</script>
