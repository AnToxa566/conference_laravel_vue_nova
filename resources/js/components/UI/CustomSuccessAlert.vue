<template>
    <v-alert
        v-if="this.successMessage"
        type="success"
        variant="tonal"
    >
        {{ this.successMessage }}
    </v-alert>
</template>


<script>
export default {
    name: 'custom-success-alert',

    props: {
        successMessage: {
            type: String,
            required: true,
            default: '',
        },
    },
}
</script>
