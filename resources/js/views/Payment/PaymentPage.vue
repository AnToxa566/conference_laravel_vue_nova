<template>
    <div>
        <custom-header>
            <template v-slot:header>Payment</template>
        </custom-header>

        <payment-methods-list
            @addPaymentMethod="openPaymentForm"
        ></payment-methods-list>

        <v-dialog
            v-model="paymentFormDialog"
            class="payment-form"
        >
            <v-card class="pa-8">
                <payment-form></payment-form>
            </v-card>
        </v-dialog>

        <v-snackbar
            v-model="this.snackbar"
            rounded="pill"
            color="success"
        >
            Subscription has been updated!
        </v-snackbar>
    </div>
</template>


<script>
import PaymentForm from '../../components/Payment/PaymentForm.vue'
import PaymentMethodsList from '../../components/Payment/PaymentMethodsList.vue'

import { mapGetters } from 'vuex'

export default {
    components: {
        PaymentForm,
        PaymentMethodsList,
    },

    data: () => ({
        paymentFormDialog: false,
        snackbar: false,
    }),

    computed: {
        ...mapGetters({
            loadStatuses: 'plan/loadStatuses',
            updateSubscriptionStatus: 'plan/updateSubscriptionStatus',
        }),

        isSubscriptionUpdated() {
            return this.updateSubscriptionStatus === this.loadStatuses.COMPLETED
        },
    },

    methods: {
        openPaymentForm() {
            this.paymentFormDialog = true
        }
    },

    watch: {
        isSubscriptionUpdated(newValue) {
            this.snackbar = newValue
        }
    }
}
</script>


<style scoped>
    .payment-form {
        max-width: 600px;
    }
</style>
