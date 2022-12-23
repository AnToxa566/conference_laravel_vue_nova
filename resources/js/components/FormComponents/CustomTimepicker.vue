<template>
    <Datepicker
        dark
        timePicker
        model-type="HH:mm:ss"

        :placeholder=this.placeholder
        :minTime=this.min
        :maxTime=this.max

        :modelValue="modelValue"
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
    name: 'custom-timepicker',

    props: {
        modelValue: [Object],

        placeholder: {
            type: String,
            required: false,
            default: 'Input the time'
        },

        min: {
            type: Object,
            required: false,
            default: {},
        },

        max: {
            type: Object,
            required: false,
            default: {},
        },

        hideDetails: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    methods: {
        updateInput(event) {
            this.$emit('update:modelValue', event)

            if (!this.hideDetails) {
                const messageWrapper = document.getElementById("message__wrapper")
                this.modelValue ? messageWrapper.classList.remove("hidden__message") : messageWrapper.classList.add("hidden__message")
            }
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
