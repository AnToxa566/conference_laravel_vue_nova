<template>
    <div>
        <div
            v-if="this.paymentMethodsLoadStatus === this.loadStatuses.STARTED"
            class="d-flex justify-content-center">
            <v-progress-circular indeterminate></v-progress-circular>
        </div>

        <div
            v-show="this.paymentMethodsLoadStatus == this.loadStatuses.COMPLETED && this.paymentMethods.length == 0"
            class="text-center"
        >
            <p class="text-body-1">No payment methods found, please add a payment method.</p>

            <payment-methods-add-button
                @click="addPaymentMethodBtnClicked"
            ></payment-methods-add-button>
        </div>

        <div
            v-show="paymentMethodsLoadStatus == this.loadStatuses.COMPLETED && paymentMethods.length > 0"
        >
            <div class="py-3 mb-2 text-h6 font-weight-bold">
                Choose payment method
            </div>

            <payment-methods-list-item
                v-for="(method, key) in this.paymentMethods"
                class="mb-4"

                :class="{ 'bg-success text-light': this.paymentMethodSelected === method.id }"
                :key="'method-'+key"
                :method="method"

                @click="this.paymentMethodSelected = method.id"
            ></payment-methods-list-item>

            <div class="d-flex align-center mt-2">
                <payment-methods-pay-button
                    :paymentMethodId="this.paymentMethodSelected"
                    class="me-2"
                ></payment-methods-pay-button>

                <payment-methods-add-button
                    variant="text"
                    @click="addPaymentMethodBtnClicked"
                ></payment-methods-add-button>
            </div>
        </div>
    </div>
</template>


<script>
import PaymentMethodsListItem from './PaymentMethodsListItem.vue'
import PaymentMethodsAddButton from './PaymentMethodsAddButton.vue'
import PaymentMethodsPayButton from './PaymentMethodsPayButton.vue'

import { mapGetters, mapActions } from 'vuex'

export default {
    name: 'payment-methods-list',

    emits: ['addPaymentMethod'],

    components: {
        PaymentMethodsListItem,
        PaymentMethodsAddButton,
        PaymentMethodsPayButton,
    },

    data: () => ({
        paymentMethodSelected: '',
    }),

    created() {
        this.loadPaymentMethods()
    },

    computed: {
        ...mapGetters({
            loadStatuses: 'plan/loadStatuses',
            paymentMethods: 'payment/paymentMethods',
            paymentMethodsLoadStatus: 'payment/paymentMethodsLoadStatus',
        }),
    },

    methods: {
        ...mapActions({
            loadPaymentMethods: 'payment/loadPaymentMethods',
        }),

        addPaymentMethodBtnClicked() {
            this.$emit('addPaymentMethod')
        }
    },
}
</script>
