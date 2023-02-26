<template>
    <div>
        <v-snackbar
            :modelValue="modelValue"
            rounded="pill"
            color="error"
        >
            {{ this.text }}
        </v-snackbar>
    </div>

</template>


<script>
export default {
    name: 'joins-ended-flash-message',

    props: {
        modelValue: [Boolean],

        text: {
            type: String,
            required: true,
            default: '',
        }
    },
}
</script>
