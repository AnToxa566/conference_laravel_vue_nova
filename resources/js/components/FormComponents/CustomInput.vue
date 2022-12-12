<template>
    <v-text-field
        variant="solo"

        :model-value="modelValue"
        @input="updateInput"
    ></v-text-field>
</template>


<script>
export default {
    name: 'custom-input',

    props: {
        modelValue: [String, Number],
    },

    methods: {
        updateInput(event) {
            this.$emit('update:modelValue', event.target.value)
        },
    },
}
</script>
