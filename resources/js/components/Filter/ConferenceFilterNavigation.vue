<template>
    <v-expansion-panels
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
        class="text-red-darken-1 my-4"
        style="cursor: pointer;"
        @click="reserFilters"
    >
        Reset filters
    </div>
</template>


<script>
export default {
    data: () => ({
        filter: {
            lecturesCountRange: [0, 0],
            dateAfter: null,
            dateBefore: null,
            selectedCategoriesId: [],
        },

    }),

    created() {
        this.filter.lecturesCountRange = [this.minLecturesCount, this.maxLecturesCount]
        this.updateFilters()
    },

    computed: {
        minLecturesCount() {
            return this.$store.getters['conference/getMinCountLectures']
        },
        maxLecturesCount() {
            return this.$store.getters['conference/getMaxCountLectures']
        },

        minDateEvent() {
            return this.$store.getters['conference/getMinDateEvent']
        },
        maxDateEvent() {
            return this.$store.getters['conference/getMaxDateEvent']
        },

        categories() {
            return this.$store.getters['category/categories']
        }
    },

    methods: {
        updateFilters() {
            this.$store.commit('conference/SET_FILTERED_CONFERENCES', [])
            this.$store.dispatch('conference/fetchFilteredConferences', this.filter)
        },

        reserFilters() {
            this.filter.lecturesCountRange = [this.minLecturesCount, this.maxLecturesCount]
            this.filter.dateAfter = null
            this.filter.dateBefore = null
            this.filter.selectedCategoriesId = []

            this.updateFilters()
        },
    },
}
</script>
