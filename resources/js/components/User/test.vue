<template>
    <div>
        <custom-header>
            <template v-slot:header>Subscription</template>
        </custom-header>

        <custom-input
            v-model="name"
            id="card-holder-name"
        >
        </custom-input>
        <div id="card-element"></div>

        <v-btn
            id="add-card-button"
            @click="submitPaymentMethod"
        >
            Save Payment Method
        </v-btn>

        <div v-show="paymentMethodsLoadStatus == 2 && paymentMethods.length == 0">
            No payment method on file, please add a payment method.
        </div>

        <div v-show="paymentMethodsLoadStatus == 2 && paymentMethods.length > 0">
            <div
                v-for="(method, key) in paymentMethods"
                class="border rounded row p-1"

                :class="{ 'bg-success text-light': paymentMethodSelected == method.id }"
                :key="'method-'+key"

                @click="paymentMethodSelected = method.id"
            >
                <div class="col-2">
                    {{ method.brand.charAt(0).toUpperCase() }}{{ method.brand.slice(1) }}
                </div>

                <div class="col-7">
                    Ending In: {{ method.last_four }} Exp: {{ method.exp_month }} / {{ method.exp_year }}
                </div>

                <div class="col-3">
                    <span @click.stop="removePaymentMethod(method.id)"> Remove </span>
                </div>
            </div>
        </div>

        <div
            v-show="plans.length > 0"
            class="mt-8"
        >
            <div
                v-for="plan in plans"
                :key="plan.id"
                class="mt-3 row rounded border"
                :class="{'bg-success text-light': selectedPlan == plan.stripe_plan}"
                @click="selectedPlan = plan.stripe_plan"
            >
                <div class="col-6">
                    {{ plan.name }}
                </div>

                <div class="col-6">
                    ${{ plan.price }}/mo.
                </div>
            </div>
        </div>

        <v-btn
            @click="updateSubscription"
        >
            Subscribe
        </v-btn>
    </div>
</template>


<script>
export default {
    data: () => ({
        stripeAPIToken: import.meta.env.VITE_STRIPE_KEY,

        stripe: '',
        elements: '',
        card: '',

        intentToken: '',

        name: '',

        addPaymentStatus: 0,
        addPaymentStatusError: '',

        paymentMethods: [],
        paymentMethodsLoadStatus: 0,
        paymentMethodSelected: {},

        selectedPlan: '',
    }),

    mounted() {
        this.includeStripe('js.stripe.com/v3/', function() {
            this.configureStripe();
        }.bind(this))

        this.loadPlans()
        this.loadIntent()
        this.loadPaymentMethods()
    },

    computed: {
        plans() {
            return this.$store.getters['plan/plans']
        }
    },

    methods: {
        /*
            Includes Stripe.js dynamically
        */
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

        /*
            Configures Stripe by setting up the elements and creating the card element.
        */
        configureStripe() {
            this.stripe = Stripe(this.stripeAPIToken);

            this.elements = this.stripe.elements();
            this.card = this.elements.create('card');

            this.card.mount('#card-element');
        },

        /*
            Loads the payment intent key for the user to pay.
        */
        loadIntent() {
            axios.get('/api/user/setup-intent', JSON.parse(localStorage.getItem('config')))
                .then(function(res) {
                    this.intentToken = res.data;
                }.bind(this));
        },

        /*
            Receives a payment method for the user.
        */
        submitPaymentMethod() {
            this.addPaymentStatus = 1

            this.stripe.confirmCardSetup(
                this.intentToken.client_secret, {
                    payment_method: {
                        card: this.card,
                        billing_details: {
                            name: this.name
                        }
                    }
                }
            ).then(function(result) {
                if (result.error) {
                    this.addPaymentStatus = 3;
                    this.addPaymentStatusError = result.error.message;
                }
                else {
                    this.savePaymentMethod(result.setupIntent.payment_method);
                    this.addPaymentStatus = 2;
                    this.card.clear();
                    this.name = '';
                }
            }.bind(this));
        },

        /*
            Saves the payment method for the user and re-loads the payment methods.
        */
        savePaymentMethod(method) {
            axios.post('/api/user/payments', { payment_method: method }, JSON.parse(localStorage.getItem('config')))
                .then(function() {
                    this.loadPaymentMethods();
                }.bind(this));
        },

        /*
            Loads all of the payment methods for the user.
        */
        loadPaymentMethods() {
            this.paymentMethodsLoadStatus = 1;

            axios.get('/api/user/payment-methods', JSON.parse(localStorage.getItem('config')))
                .then(function(response) {
                    this.paymentMethods = response.data;

                    this.paymentMethodsLoadStatus = 2;
                }.bind(this));
        },

        /*
            Removes the payment method by payment's id for the user.
        */
        removePaymentMethod(paymentID) {
            axios.post('/api/user/remove-payment', { id: paymentID }, JSON.parse(localStorage.getItem('config')))
                .then(function(response) {
                    this.loadPaymentMethods();
                }.bind(this));
        },

        loadPlans() {
            this.$store.dispatch('plan/fetchPlans')
        },

        updateSubscription() {
            axios.put('/api/plans/subscription', {
                plan: this.selectedPlan,
                payment: this.paymentMethodSelected
            }, JSON.parse(localStorage.getItem('config')))
                .then(function(response) {
                    alert('You Are Subscribed!');
                }.bind(this));
        },
    },
}
</script>
