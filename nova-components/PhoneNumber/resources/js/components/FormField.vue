<template>
    <DefaultField
        :field="field"
        :errors="errors"
        :show-help-text="showHelpText"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <input
                :id="field.attribute"
                type="text"
                class="w-full form-control form-input form-input-bordered"
                :class="errorClasses"
                :placeholder="field.name"
                v-model="value"
            />
        </template>
    </DefaultField>
</template>


<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import { parsePhoneNumber, isValidPhoneNumber } from 'libphonenumber-js'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    data: () => ({
        isValid: false,
        phoneNumber: null,
    }),

    methods: {
        /*
        * Set the initial, internal value for the field.
        */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        /**
        * Fill the given FormData object with the field's internal value.
        */
        fill(formData) {
            formData.append(this.field.attribute, this.isValid ? this.phoneNumber.number : 'NOT_VALID')

            if (this.field.countryPhoneCodeAttribute) {
                formData.append(this.field.countryPhoneCodeAttribute, this.isValid ? this.phoneNumber.country : 'NOT_VALID')
            }
        },
    },

    watch: {
        value(newValue) {
            const phoneNumber = parsePhoneNumber(newValue)

            if (phoneNumber) {
                this.value = phoneNumber.formatInternational()
                this.isValid = isValidPhoneNumber(newValue)

                this.phoneNumber = phoneNumber
            }
        }
    }
}
</script>
