<template>
    <custom-header>
        <template v-slot:header>Update conference</template>
    </custom-header>

    <custom-error-alert
        :errorMessage='this.error'
        class="mb-6"
    >
    </custom-error-alert>

    <v-form
        action=""
        @submit="update"
        ref="form"
        v-model="valid"
    >
        <!-- Title -->

        <v-text-field
            v-model="conference.title"
            label="Topic"
            :counter="255"
            :rules="titleRules"
            variant="solo"
            placeholder="Enter a topic"
            required
        ></v-text-field>

        <!-- Date & Time -->

        <Datepicker
            placeholder="When"
            :dark="true"
            v-model="conference.date_time_event"
            @update:modelValue="hiddenMessage"
            modelType="yyyy-MM-dd HH:mm:ss"
            :minDate="new Date()"
        />

        <div id="message__wrapper" class="hidden__message">
            <p class="message">Date is required!</p>
        </div>

        <!-- Address -->

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

        <!-- Map -->

        <v-card class="mb-4">
            <GMapMap
                :center="latLng"
                :zoom="16"
                :options="{
                    zoomControl: false,
                    scaleControl: false,
                    streetViewControl: false,
                }"
                map-type-id="roadmap"
                class="w-100"
                style="height: 500px"
                @click="updateMakerPosition"
            >
                <GMapMarker
                    :position="latLng"
                    :clickable="true"
                    :draggable="true"
                    :visible="markerVisible"
                    @dragend="updateMakerPosition"
                />
            </GMapMap>
        </v-card>

        <!-- Country -->

        <country-selected
            v-model="conference.country"
            :rules="[v => !!v || 'Country is required!']"
        >
        </country-selected>

        <!-- Category -->

        <category-selected
            :roots="this.roots"
            :nodes="this.nodes"

            :defaultSelect="{
                text: this.category ? this.category.title : '',
                id: this.category ? this.category.id : '',
            }"

            @select="categorySelected"
            @clear="categoryClear"
        >
        </category-selected>

        <!-- Buttons -->

        <div class="d-flex justify-content-start">
            <v-btn type="submit" variant="tonal" color="white" class="me-2" @click.prevent="update"> Save </v-btn>
            <conference-delete-button :conferenceId="this.id"></conference-delete-button>
        </div>
    </v-form>
</template>


<script>
import ConferenceDeleteButton from '../../components/Conference/ConferenceDeleteButton.vue';

export default {
    components: {
        ConferenceDeleteButton
    },

    data: () => ({
        id: null,
        valid: false,

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
    }),

    created() {
        this.id = parseInt(this.$route.params.id, 10);
        this.$store.dispatch('conference/fetchDetailConference', this.id)
        this.$store.commit('conference/SET_ERROR', '')
    },

    computed: {
        conference() {
            return this.$store.getters['conference/conference']
        },
        conferenceById() {
            return this.$store.getters['conference/conferenceById'](this.id)
        },
        category() {
            return this.$store.getters['category/categoryById'](this.conferenceById.category_id)
        },

        roots() {
            return this.$store.getters['category/roots']
        },
        nodes() {
            return this.$store.getters['category/nodes']
        },

        error() {
            return this.$store.getters['conference/error']
        },

        latLng() {
            if (this.conference.latitude == '' || this.conference.longitude == '' || this.conference.latitude == null || this.conference.longitude == null) {
                return {
                    lat: parseFloat(import.meta.env.VITE_DEFAULT_LATITUDE, 10),
                    lng: parseFloat(import.meta.env.VITE_DEFAULT_LONGITUDE, 10),
                }
            }
            else {
                return {
                    lat: parseFloat(this.conference.latitude, 10),
                    lng: parseFloat(this.conference.longitude, 10),
                }
            }
        },
        markerVisible() {
            return this.conference.latitude !== '' && this.conference.longitude !== ''
        },
    },

    methods: {
        hiddenMessage() {
            let message = document.getElementById("message__wrapper")
            message.classList.add("hidden__message")
        },
        updateMakerPosition: function(event) {
            this.conference.latitude = event.latLng.lat().toFixed(4)
            this.conference.longitude = event.latLng.lng().toFixed(4)
        },

        categorySelected(event) {
            this.conference.category_id = parseInt(event.id, 10)
        },
        categoryClear(event) {
            this.conference.category_id = null
        },

        async update() {
            const { valid } = await this.$refs.form.validate()

            if (this.conference.date_time_event == '') {
                let message = document.getElementById("message__wrapper")
                message.classList.remove("hidden__message")
            }
            else {
                if (valid) {
                    this.conference.id = this.id
                    this.$store.dispatch('conference/updateConference', this.conference)
                }
            }
        },
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
