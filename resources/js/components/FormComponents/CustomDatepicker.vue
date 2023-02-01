<template>
    <div>
        <Datepicker
            dark
            modelType="yyyy-MM-dd"

            :placeholder=this.placeholder
            :minDate=this.min
            :maxDate=this.max

            :modelValue="modelValue"
            :enableTimePicker="false"
            @update:modelValue="updateInput"
        />

        <div
            v-if="!hideDetails && !this.modelValue"
        >
            <p class="message">Field is required!</p>
        </div>
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

        min: {
            type: Date,
            required: false,
            default: new Date(1800, 1, 1),
        },

        max: {
            type: Date,
            required: false,
            default: new Date(2100, 1, 1),
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
        margin-bottom: 0px;
    }
</style>
