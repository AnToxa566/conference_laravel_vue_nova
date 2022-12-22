<template>
    <v-expansion-panels
        variant="accordion"
        multiple
    >
        <filter-range-slider
            v-model="sliderRange"

            :min="minLecturesCount"
            :max="maxLecturesCount"
        >
            <template v-slot:title> Number of lectures </template>
        </filter-range-slider>

        <filter-date>
            <template v-slot:title> Date, after </template>
        </filter-date>

        <filter-date>
            <template v-slot:title> Date, before </template>
        </filter-date>

        <filter-multiple-selects>
            <template v-slot:title> Category </template>
        </filter-multiple-selects>
    </v-expansion-panels>
</template>


<script>
export default {
    data: () => ({
        sliderRange: [0, 0]
    }),

    computed: {
        minLecturesCount() {
            return this.$store.getters['conference/getMinCountLectures']
        },

        maxLecturesCount() {
            return this.$store.getters['conference/getMaxCountLectures']
        },
    },

    mounted() {
        this.sliderRange = [this.minLecturesCount, this.maxLecturesCount]
    },
}
</script>
