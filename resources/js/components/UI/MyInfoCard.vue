<template>
    <v-card class="py-4 px-8 mb-4" variant="tonal">
        <v-row>
            <v-col cols="2">
                <p class="text-grey font-weight-bold"> <slot name="header"></slot> </p>
            </v-col>
            <v-col>
                <p> <slot name="body"></slot> </p>
                <slot name="map"></slot>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
export default {

}
</script>

<style scoped>

</style>
