<template>
    <v-alert
        v-if="this.errorMessage"
        type="error"
        variant="tonal"
    >
        {{ this.errorMessage }}
    </v-alert>
</template>


<script>
export default {
    name: 'custom-error-alert',

    props: {
        errorMessage: {
            type: String,
            required: true,
            default: '',
        },
    },
}
</script>
