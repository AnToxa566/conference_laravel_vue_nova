<template>
    <MazPhoneNumberInput
        :model-value="modelValue"
        @update="updateInput"

        :preferred-countries="['UA']"
        :default-country-code="countryCode"
        :success="phoneResults?.isValid"
    />
</template>


<script>
export default {
    name: 'custom-phone-number-input',

    data: () => ({
        phoneResults: null,
    }),

    props: {
        modelValue: [String, Number],

        countryCode: {
            type: String,
            required: true,
            default: 'UA',
        },
    },

    methods: {
        updateInput(event) {
            this.phoneResults = event

            this.$emit('update:modelValue', event?.e164)
            this.$emit('updateValue', event?.isValid)
        },
    },
}
</script>


<style>
    .m-input-wrapper-right, .m-input-wrapper-input {
        background-color: rgb(33, 33, 33);
    }

    .maz-border {
        border-width: 0;
    }
</style>
