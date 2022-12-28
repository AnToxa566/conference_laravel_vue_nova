<template>
    <v-card
        v-if="loading"
        class="pa-4 mb-4"
        variant="tonal"
    >
        <v-card-title class="shimmer w-50 py-4 my-4 ms-4 bg-grey-darken-4 rounded-pill"></v-card-title>

        <v-card-subtitle class="shimmer w-25 py-2 my-2 ms-4 bg-grey-darken-4 rounded-pill"></v-card-subtitle>
        <v-card-subtitle class="shimmer w-25 py-2 my-2 ms-4 bg-grey-darken-4 rounded-pill"></v-card-subtitle>

        <v-card-actions class="d-flex">
            <v-btn variant="tonal" color="white" class="shimmer mx-1 px-10"></v-btn>
            <v-btn variant="tonal" color="white" class="shimmer mx-1 px-10"></v-btn>
        </v-card-actions>
    </v-card>

    <div
        v-else
        class="text-subtitle-1 font-weight-medium text-center"
    >
        Conferences not found
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
