<template>
    <v-expansion-panel>
        <v-expansion-panel-title>
            <slot name="title"></slot>
        </v-expansion-panel-title>

        <!-- TODO: v-model -->

        <v-expansion-panel-text
            class="py-2"
        >
            <div
                class="d-flex justify-space-between mb-4"
            >
                <v-text-field
                    v-model="modelValue[0]"

                    type="number"
                    variant="solo"
                    density="compact"
                    class="mx-1"

                    hide-details
                ></v-text-field>

                <v-text-field
                    v-model="modelValue[1]"

                    type="number"
                    variant="solo"
                    density="compact"
                    class="mx-1"

                    hide-details
                ></v-text-field>
            </div>

            <v-range-slider
                :model-value="modelValue"
                :step="1"

                :min="min"
                :max="max"

                color="white"

                thumb-label
                hide-details

                @update:modelValue="updateSlider"
            >
            </v-range-slider>
        </v-expansion-panel-text>
    </v-expansion-panel>
</template>


<script>
export default {
    props: {
        modelValue: [Array],

        min: {
            type: Number,
            required: false,
            default: 0,
        },

        max: {
            type: Number,
            required: false,
            default: 100,
        },
    },

    methods: {
        updateSlider(event) {
            this.$emit('update:modelValue', event)
        }
    },
}
</script>
