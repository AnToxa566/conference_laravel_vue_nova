<template>
    <v-expansion-panel>
        <v-expansion-panel-title>
            <slot name="title"></slot>
        </v-expansion-panel-title>

        <v-expansion-panel-text>
            <custom-timepicker
                :modelValue="modelValue"

                :min="minTime"
                :max="maxTime"

                @update:modelValue="updateDate"
            >
            </custom-timepicker>
        </v-expansion-panel-text>
    </v-expansion-panel>
</template>


<script>
export default {
    props: {
        modelValue: [Object],

        minTime: {
            type: Object,
            required: false,
            default: {},
        },

        maxTime: {
            type: Object,
            required: false,
            default: {},
        },
    },

    methods: {
        updateDate(event) {
            this.$emit('update:modelValue', event)
        }
    },
}
</script>
