<template>
    <DefaultField
        :field="field"
        :errors="errors"
        :show-help-text="showHelpText"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <GoogleMap
                api-key="AIzaSyAUH_gJhMm19A-BF1KDzmtNX7eiaZbpW1g"
                style="width: 100%; height: 500px"
                :center="this.markerPosition"
                :zoom="16"
                :zoomControl="false"
                @click="updateMakerPosition"
            >
                <Marker
                    :options="{
                        position: this.markerPosition,
                        draggable: true,
                        visible: this.markerVisible,
                    }"
                    @dragend="updateMakerPosition"
                />
            </GoogleMap>

            <div class="flex mt-2">
                <input
                    type="number"
                    class="w-full mr-2 form-control form-input form-input-bordered"
                    placeholder="Latitude"
                    step="any"
                    :min="-90"
                    :max="90"
                    v-model="field.lat"
                />

                <input
                    type="number"
                    class="w-full ml-2 form-control form-input form-input-bordered"
                    placeholder="Longitude"
                    step="any"
                    :min="-180"
                    :max="180"
                    v-model="field.lng"
                />
            </div>
        </template>
    </DefaultField>
</template>


<script>
import { GoogleMap, Marker } from "vue3-google-map";
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    name: 'google-maps',

    mixins: [FormField, HandlesValidationErrors],

    components: { GoogleMap, Marker },

    props: ['resourceName', 'resourceId', 'field'],

    data: () => ({
        defaultCenter: { lat: '', lng: '' }
    }),

    mounted() {
        this.defaultCenter.lat = this.field.lat ?? 47.83992
        this.defaultCenter.lng = this.field.lng ?? 35.12592

        this.field.lat = ''
        this.field.lng = ''
    },

    computed: {
        markerPosition() {
            if (this.field.lat !== '' && this.field.lng !== '') {
                return { lat: this.field.lat, lng: this.field.lng }
            }

            return  this.defaultCenter
        },

        markerVisible() {
            return this.field.lat !== '' && this.field.lng !== ''
        },
    },

    methods: {
        /**
        * Fill the given FormData object with the field's internal value.
        */
        fill(formData) {
            formData.append(this.field.latitude, this.field.lat && this.field.lng ? this.field.lat : '');
            formData.append(this.field.longitude, this.field.lat && this.field.lng ? this.field.lng : '');
        },

        /**
        * Update marker position values when clicking on the map or moving the marker.
        */
        updateMakerPosition(event) {
            this.field.lat = parseFloat(event.latLng.lat().toFixed(4), 10)
            this.field.lng = parseFloat(event.latLng.lng().toFixed(4), 10)
        },
    },
}
</script>
