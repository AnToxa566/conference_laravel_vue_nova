<template>
    <v-card
        class="py-4 px-6"
        style="cursor: pointer;"
    >
        <div class="d-flex justify-content-between align-center">
            <div class="font-weight-bold">
                <span class="text-capitalize">{{ method.brand }}</span> card ending in {{ method.last_four }}
            </div>

            <v-btn
                size="small"
                color="error"
                @click.stop="removePaymentMethod"
            >
                Remove
            </v-btn>
        </div>
    </v-card>
</template>


<script>
export default {
    name: 'payment-methods-list-item',

    props: {
        method: {
            type: Object,
            required: true,
        }
    },

    methods: {
        removePaymentMethod() {
            alert('Сoming soon!')
        },
    },
}
</script>
