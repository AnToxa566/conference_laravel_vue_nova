<template>
    <v-autocomplete
        variant="solo"
        label="Country"
        no-data-text="Country name is incorrect"

        :items="countries"

        :model-value="modelValue"
        @update:modelValue="updateValue"
        @update:search="onSearch"
    ></v-autocomplete>
</template>


<script>
export default {
    name: 'country-selected',

    data: () => ({
        allCountries: [],
        countries: [],
    }),

    props: {
        modelValue: [String],
    },

    created() {
        this.allCountries = this.$store.getters['country/countries'].map(country => country.name)
    },

    methods: {
        updateValue(event) {
            this.$emit('update:modelValue', event)
        },

        onSearch(event) {
            this.countries = this.allCountries.filter(country => country.toLowerCase().includes(event.toLowerCase())).slice(0, 6)
        },
    },
}
</script>
