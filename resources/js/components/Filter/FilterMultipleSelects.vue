<template>
    <v-expansion-panel>
        <v-expansion-panel-title>
            <slot name="title"></slot>
        </v-expansion-panel-title>

        <v-expansion-panel-text
            class="py-2"
        >
            <v-select
                :model-value="modelValue"
                :items="selectItems"

                item-title="title"
                item-value="id"

                label="Select"
                variant="solo"

                chips
                multiple
                clearable
                hide-details

                @update:modelValue="updateSelect"
            ></v-select>
        </v-expansion-panel-text>
    </v-expansion-panel>
</template>


<script>
export default {
    props: {
        modelValue: [Array],

        selectItems: {
            type: Array,
            required: false,
            default: [],
        },
    },

    methods: {
        updateSelect(event) {
            this.$emit('update:modelValue', event)
        }
    },
}
</script>
