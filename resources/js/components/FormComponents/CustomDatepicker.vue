<template>
    <Datepicker
        dark
        modelType="yyyy-MM-dd"

        :placeholder=this.placeholder
        :minDate=this.minDate
        :maxDate=this.maxDate

        :modelValue="modelValue"
        :enableTimePicker="false"
        @update:modelValue="updateInput"
    />

    <div
        v-if="!hideDetails"
        id="message__wrapper"
        class="hidden__message"
    >
        <p class="message">Field is required!</p>
    </div>
</template>


<script>
export default {
    name: 'custom-datepicker',

    props: {
        modelValue: [String],

        placeholder: {
            type: String,
            required: false,
            default: 'Enter a date'
        },

        maxDate: {
            type: Date,
            required: false,
            default: new Date(2100, 1, 1),
        },

        minDate: {
            type: Date,
            required: false,
            default: new Date(1800, 1, 1),
        },

        hideDetails: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    methods: {
        updateInput(event) {
            const messageWrapper = document.getElementById("message__wrapper")

            this.$emit('update:modelValue', event)
            this.modelValue ? messageWrapper.classList.remove("hidden__message") : messageWrapper.classList.add("hidden__message")
        },
    },
}
</script>


<style>
    .dp__input {
        padding: 16px 12px;
    }
    .dp__input_icon_pad {
        padding-left: 35px;
    }

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
