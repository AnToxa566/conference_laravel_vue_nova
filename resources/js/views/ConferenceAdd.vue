<template>
    <p class="text-h4 mb-4">Adding</p>

    <v-form
        action=""
        @submit="add(conference)"
        ref="form"
        v-model="valid"
    >
        <v-text-field
            v-model="conference.title"
            label="Topic"
            :counter="255"
            :rules="titleRules"
            variant="solo"
            placeholder="Enter a topic"
            required
        ></v-text-field>

        <Datepicker
            placeholder="When"
            dark="true"
            v-model="conference.date_time_event"
            @update:modelValue="hiddenMessage"
            modelType="yyyy-MM-dd HH:mm:ss"
            :minDate="new Date()"
        />

        <div id="message__wrapper" class="hidden__message">
            <p class="message">Date is required!</p>
        </div>

        <v-row>
            <v-col cols="6">
                <v-text-field
                    type="number"
                    v-model="conference.latitude"
                    :rules="latitudeRules"
                    label="Latitude"
                    variant="solo"
                    placeholder="Enter a latitude"
                ></v-text-field>
            </v-col>

            <v-col cols="6">
                <v-text-field
                    type="number"
                    v-model="conference.longitude"
                    :rules="longitudeRules"
                    label="Longitude"
                    variant="solo"
                    placeholder="Enter a longitude"
                ></v-text-field>
            </v-col>
        </v-row>

        <my-map></my-map>

        <v-select
            v-model="conference.country"
            :items="countries"
            :rules="[v => !!v || 'Country is required!']"
            variant="solo"
            label="Country"
            required
        ></v-select>

        <v-row>
            <v-col cols="2">
                <v-btn variant="tonal" color="white" class="w-100" @click="$router.go(-1)"> Back </v-btn>
            </v-col>

            <v-col cols="2">
                <v-btn type="submit" variant="tonal" color="success" class="w-100" @click.prevent="add(conference)"> Save </v-btn>
            </v-col>
        </v-row>
    </v-form>
</template>


<script>
import MyMap from '../components/UI/MyMap.vue'

export default {
    components: {
        MyMap,
    },

    data: () => ({
        valid: false,

        conference: {
            title: '',
            date_time_event: '',
            latitude: '',
            longitude: '',
            country: '',
        },

        titleRules: [
            v => !!v || 'Topic is required!',
            v => (v && v.length >= 2) || 'Topic must be 2 characters or longer!',
            v => (v && v.length <= 255) || 'Topic must be less than 256 characters!',
        ],

        latitudeRules: [
            v => ('' || v >= -90 && v <= 90) || 'Latitude value must be range -90 to 90!',
        ],

        longitudeRules: [
            v => ('' || v >= -180 && v <= 180) || 'Latitude value must be range -180 to 180!',
        ],

        countries: ['Foo', 'Bar', 'Fizz', 'Buzz'],
    }),

    methods: {
        hiddenMessage() {
            let message = document.getElementById("message__wrapper")
            message.classList.add("hidden__message")
        },
        async add(conference) {
            const { valid } = await this.$refs.form.validate()

            if (this.conference.date_time_event === '') {
                let message = document.getElementById("message__wrapper")
                message.classList.remove("hidden__message")
            }
            else {
                if (valid) {
                    this.$store.dispatch('conference/storeConference', conference)
                }
            }
        }
    }
};
</script>


<style scoped>
    .hidden__message {
        visibility: hidden;
    }
    .message {
        color: #cf6679;
        font-size: 12px;
        padding: 6px 16px 0 16px;
        margin-bottom: 0px;
    }
</style>
