<template>
    <v-card
        v-if="loading"
        class="pa-4 mb-4"
        variant="tonal"
    >
        <v-card-title class="shimmer w-50 py-4 my-4 ms-4 bg-grey-darken-4 rounded-pill"></v-card-title>

        <v-card-subtitle class="shimmer w-25 py-2 my-4 ms-4 bg-grey-darken-4 rounded-pill"></v-card-subtitle>
        <v-card-subtitle class="shimmer w-25 py-2 my-4 ms-4 bg-grey-darken-4 rounded-pill"></v-card-subtitle>
        <v-card-subtitle class="shimmer w-25 py-2 my-4 ms-4 bg-grey-darken-4 rounded-pill"></v-card-subtitle>
    </v-card>

    <div
        v-else
        class="text-subtitle-1 font-weight-medium text-center"
    >
        Lectures not found
    </div>
</template>


<script>
export default {
    data: () => ({
        loading: true,
    }),

    mounted() {
        setTimeout(() => (this.loading = false), 4000)
    },
}
</script>


<style>
    .shimmer {
        color: lightgray;

        -webkit-mask:linear-gradient(-60deg,rgb(30, 30, 30) 30%,rgba(45, 45, 45, 0.333),rgb(30, 30, 30) 70%) right/300% 100%;
        background-repeat: no-repeat;
        animation: shimmer 2.5s infinite;
    }

    @keyframes shimmer {
        100% {-webkit-mask-position:left}
    }
</style>
