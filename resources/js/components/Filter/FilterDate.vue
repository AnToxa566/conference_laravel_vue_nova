<template>
    <v-expansion-panel>
        <v-expansion-panel-title>
            <slot name="title"></slot>
        </v-expansion-panel-title>

        <v-expansion-panel-text>
            <custom-datepicker
                :modelValue="modelValue"

                :min="minDate"
                :max="maxDate"
                :hideDetails="true"

                @update:modelValue="updateDate"
            >
            </custom-datepicker>
        </v-expansion-panel-text>
    </v-expansion-panel>
</template>


<script>
export default {
    props: {
        modelValue: [String],

        minDate: {
            type: Date,
            required: false,
            default: new Date(1800, 1, 1),
        },

        maxDate: {
            type: Date,
            required: false,
            default: new Date(2100, 1, 1),
        },
    },

    methods: {
        updateDate(event) {
            this.$emit('update:modelValue', event)
        }
    },
}
</script>
