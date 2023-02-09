<template>
    <v-expansion-panels
        v-model="panel"
        variant="accordion"
        multiple
    >

        <!-- Find conferences whose number of lectures is contained in this range -->

        <filter-range-slider
            v-model="filter.lecturesCountRange"

            :min="minLecturesCount"
            :max="maxLecturesCount"

            @update:modelValue="updateFilters"
        >
            <template v-slot:title> Number of lectures </template>
        </filter-range-slider>


        <!-- Find conferences which starts in this date or after -->

        <filter-date
            v-model="filter.dateAfter"

            :minDate="new Date(minDateEvent)"
            :maxDate="new Date(maxDateEvent)"

            @update:modelValue="updateFilters"
        >
            <template v-slot:title> Date, after </template>
        </filter-date>


        <!-- Find conferences which starts in this date or before -->

        <filter-date
            v-model="filter.dateBefore"

            :minDate="new Date(minDateEvent)"
            :maxDate="new Date(maxDateEvent)"

            @update:modelValue="updateFilters"
        >
            <template v-slot:title> Date, before </template>
        </filter-date>


        <!-- Find conferences which contains any of these selected categories -->

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
import { mapGetters } from 'vuex'

export default {
    data: () => ({
        filter: {
            lecturesCountRange: [null, null],
            dateAfter: null,
            dateBefore: null,
            selectedCategoriesId: [],
        },

        panel: [],

        timeout: null,
    }),

    created() {
        this.$store.dispatch('conference/fetchAllConferences')
    },

    mounted() {
        this.updateFilters()
    },

    computed: {
        ...mapGetters({
            minLecturesCount: 'conference/getMinCountLectures',
            maxLecturesCount: 'conference/getMaxCountLectures',

            minDateEvent: 'conference/getMinDateEvent',
            maxDateEvent: 'conference/getMaxDateEvent',

            categories: 'category/categories',
        }),
    },

    methods: {
        updateFilters() {
            clearTimeout(this.timeout)

            this.$store.commit('conference/storeFilteredConferences', [])

            const self = this
            this.timeout = setTimeout(() => self.fetchFilteredConferences(), 800);
        },

        fetchFilteredConferences() {
            this.$store.dispatch('conference/fetchFilteredConferences', {
                minLectureCount: this.filter.lecturesCountRange[0],
                maxLectureCount: this.filter.lecturesCountRange[1],

                dateAfter: this.filter.dateAfter,
                dateBefore: this.filter.dateBefore,

                categoriesId: this.filter.selectedCategoriesId,
            })
        },

        reserFilters() {
            this.filter.lecturesCountRange = [null, null]
            this.filter.dateAfter = null
            this.filter.dateBefore = null
            this.filter.selectedCategoriesId = []

            this.panel = []

            this.updateFilters()
        },
    },
}
</script>
