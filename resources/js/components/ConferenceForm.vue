<template>
    <v-form
        ref="form"
        v-model="valid"
    >
        <v-text-field
            v-model="topic"
            label="Topic"
            :counter="255"
            :rules="topicRules"
            variant="solo"
            placeholder="Enter a topic"
            required
        ></v-text-field>

        <Datepicker
            class="mb-6"
            placeholder="When"
            dark="true"
            v-model="date"
            :minDate="new Date()"
            required
        />

        <v-row>
            <v-col cols="6">
                <v-text-field
                    type="number"
                    v-model="latitude"
                    :rules="latitudeRules"
                    label="Latitude"
                    variant="solo"
                    placeholder="Enter a latitude"
                ></v-text-field>
            </v-col>

            <v-col cols="6">
                <v-text-field
                    type="number"
                    v-model="longitude"
                    :rules="longitudeRules"
                    label="Longitude"
                    variant="solo"
                    placeholder="Enter a longitude"
                ></v-text-field>
            </v-col>
        </v-row>

        <v-card class="mb-6" id="map"></v-card>

        <v-select
            v-model="country"
            :items="countries"
            :rules="[v => !!v || 'Country is required']"
            variant="solo"
            label="Country"
            required
        ></v-select>

        <v-row>
            <v-col cols="2">
                <v-btn variant="tonal" color="white" class="w-100" @click="$router.go(-1)"> Back </v-btn>
            </v-col>

            <v-col cols="2">
                <v-btn type="submit" variant="tonal" color="success" class="w-100" @click="validate"> Save </v-btn>
            </v-col>
        </v-row>
    </v-form>
</template>

<script>
import { ref } from "vue";

export default {
    setup() {
        const date = ref();

        return {
            date,
        };
    },
    data: () => ({
        valid: true,

        topic: '',
        topicRules: [
            v => !!v || 'Topic is required',
            v => (v && v.length >= 2) || 'Topic must be 2 characters or longer',
            v => (v && v.length <= 255) || 'Topic must be less than 256 characters',
        ],

        latitude: '',
        latitudeRules: [
            v => (v && v.length >= -90 && v.length <= 90) || 'Latitude value must be range -90 to 90',
        ],

        longitude: '',
        longitudeRules: [
            v => (v && v.length >= -180 && v.length <= 180) || 'Latitude value must be range -180 to 180',
        ],

        country: null,
        countries: ['Foo', 'Bar', 'Fizz', 'Buzz'],
    }),
    methods: {
        async validate () {
            const { valid } = await this.$refs.form.validate()
            if (valid) alert('Form is valid')
        },
    }
};
</script>

<style scoped>
    #map {
        height: 300px;
    }
</style>
