<template>
    <v-expansion-panels
        variant="accordion"
        multiple
    >
        <filter-range-slider
            v-model="lecturesCountRange"

            :min="minLecturesCount"
            :max="maxLecturesCount"
        >
            <template v-slot:title> Number of lectures </template>
        </filter-range-slider>

        <filter-date
            v-model="dateAfter"

            :minDate="new Date(minDateEvent)"
            :maxDate="new Date(maxDateEvent)"
        >
            <template v-slot:title> Date, after </template>
        </filter-date>

        <filter-date
            v-model="dateBefore"

            :minDate="new Date(minDateEvent)"
            :maxDate="new Date(maxDateEvent)"
        >
            <template v-slot:title> Date, before </template>
        </filter-date>

        <filter-multiple-selects
            v-model="selectedCategoriesId"
            :selectItems="categories"
        >
            <template v-slot:title> Category </template>
        </filter-multiple-selects>
    </v-expansion-panels>
</template>


<script>
export default {
    data: () => ({
        lecturesCountRange: [0, 0],
        dateAfter: null,
        dateBefore: null,
        selectedCategoriesId: [],
    }),

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

    mounted() {
        this.lecturesCountRange = [this.minLecturesCount, this.maxLecturesCount]
    },
}
</script>
