<template>
    <div>
        <custom-error-alert
            v-show="this.isPaymentAddingFailed"
            :errorMessage='this.addPaymentStatusError'
            class="mb-6"
        >
        </custom-error-alert>

        <custom-success-alert
            v-show="this.isPaymentAdded"
            successMessage='Payment method has been added'
            class="mb-6"
        ></custom-success-alert>

        <custom-input
            v-model="firstName"
            placeholder="First name*"
        >
        </custom-input>

        <custom-input
            v-model="lastName"
            placeholder="Last name*"
        >
        </custom-input>

        <v-card
            id="card-element"
            class="pa-4 mt-4"
        ></v-card>

        <payment-methods-add-button
            class="mt-10 w-100"
            :loading="this.isPaymentAdding"
            @click="submitPaymentMethod"
        ></payment-methods-add-button>
    </div>
</template>


<script>
import PaymentMethodsAddButton from './PaymentMethodsAddButton.vue'
import { mapGetters, mapActions } from 'vuex'

export default {
    name: 'payment-form',

    components: {
        PaymentMethodsAddButton,
    },

    data: () => ({
        stripeAPIToken: import.meta.env.VITE_STRIPE_KEY,

        stripe: '',
        elements: '',
        card: '',

        firstName: '',
        lastName: '',

        addPaymentStatus: 0,
        addPaymentStatusError: '',
    }),

    created() {
        this.includeStripe('js.stripe.com/v3/', function() {
            this.configureStripe()
        }.bind(this))

        this.loadIntent()
    },

    computed: {
        ...mapGetters({
            loadStatuses: 'plan/loadStatuses',
            intentToken: 'payment/intentToken',
        }),

        isPaymentAdding() {
            return this.addPaymentStatus === this.loadStatuses.STARTED
        },

        isPaymentAdded() {
            return this.addPaymentStatus === this.loadStatuses.COMPLETED
        },

        isPaymentAddingFailed() {
            return this.addPaymentStatus === this.loadStatuses.FAILED
        },

        fullName() {
            return this.firstName + ' ' + this.lastName
        },
    },

    methods: {
        ...mapActions({
            loadIntent: 'payment/loadIntent',
            savePaymentMethod: 'payment/savePaymentMethod',
        }),

        includeStripe(URL, callback) {
            let object = document.createElement('script')
            let scriptTag = document.getElementsByTagName('script')[0]

            object.src = '//' + URL

            if (callback) {
                object.addEventListener('load', function (e) {
                    callback(null, e);
                }, false);
            }

            scriptTag.parentNode.insertBefore(object, scriptTag);
        },

        configureStripe() {
            this.stripe = Stripe(this.stripeAPIToken);

            this.elements = this.stripe.elements();
            this.card = this.elements.create('card', {
                style: {
                    base: {
                        color: '#fff',
                        fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
                        fontSize: '16px',
                        fontSmoothing: 'antialiased',
                    },
                },
            });

            this.card.mount('#card-element');
        },

        userNameValidation() {
            if (!this.firstName) {
                this.addPaymentStatus = this.loadStatuses.FAILED
                this.addPaymentStatusError = 'First name is required'
            }
            else if (!this.lastName) {
                this.addPaymentStatus = this.loadStatuses.FAILED
                this.addPaymentStatusError = 'Last name is required'
            }
        },

        submitPaymentMethod() {
            this.addPaymentStatus = this.loadStatuses.STARTED

            this.userNameValidation()

            if (this.isPaymentAddingFailed) {
                return
            }

            this.stripe.confirmCardSetup(
                this.intentToken.client_secret, {
                    payment_method: {
                        card: this.card,
                        billing_details: {
                            name: this.fullName
                        }
                    }
                }
            ).then(res => {
                if (res.error) {
                    this.addPaymentStatus = this.loadStatuses.FAILED
                    this.addPaymentStatusError = res.error.message
                }
                else {
                    this.savePaymentMethod(res.setupIntent.payment_method)
                    this.addPaymentStatus = this.loadStatuses.COMPLETED

                    this.card.clear()
                    this.firstName = ''
                    this.lastName = ''
                }
            })
        },
    },
}
</script>
