<template>
    <div>
        <Datepicker
            dark
            timePicker
            teleportCenter
            model-type="HH:mm:ss"

            :placeholder=this.placeholder
            :minTime=this.min
            :maxTime=this.max

            :modelValue="modelValue"
            @update:modelValue="updateInput"
        />

        <div>
            <p class="message"> {{ this.hint }} </p>
        </div>
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

        hint: {
            type: String,
            required: false,
            default: '',
        },
    },

    methods: {
        updateInput(event) {
            this.$emit('update:modelValue', event)
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

    .message {
        color: #cf6679;
        font-size: 12px;
        padding: 6px 16px 0 16px;
        min-height: 22px;
        margin-bottom: 0px;
    }
</style>
