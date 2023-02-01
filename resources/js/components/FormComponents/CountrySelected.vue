<template>
    <v-autocomplete
        v-model="country"
        variant="solo"
        label="Country"
        no-data-text="Country name is incorrect"

        :items="countries"
        item-title="name"
        item-value="short_code"

        @update:modelValue="updateValue"
        @update:search="onSearch"
    ></v-autocomplete>
</template>


<script>
import { mapGetters } from 'vuex'

export default {
    name: 'country-selected',

    props: {
        modelValue: [String],
    },

    data: () => ({
        country: {},
        countries: [],
    }),

    created() {
        this.country = this.allCountries.find(c => c.short_code === this.modelValue)
    },

    computed: {
        ...mapGetters({
            allCountries: 'country/countries',
        }),
    },

    methods: {
        updateValue(event) {
            this.$emit('update:modelValue', event)
        },

        onSearch(event) {
            this.countries = this.allCountries.filter(country => country.name.toLowerCase().includes(event.toLowerCase())).slice(0, 6)
        },
    },
}
</script>
