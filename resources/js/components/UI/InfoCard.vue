<template>
    <v-card class="py-4 px-8 mb-4" variant="tonal">
        <v-row>
            <v-col class="d-flex align-center" cols="2">
                <p class="text-grey font-weight-bold mb-0"> <slot name="header"></slot> </p>
            </v-col>
            <v-col class="d-flex align-center">
                <p class="mb-0"> <slot name="body"></slot> </p>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
export default {
    name: 'info-card',
}
</script>
