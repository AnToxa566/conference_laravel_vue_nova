<template>
    <v-btn
        variant="tonal"
        color="white"

        :loading="this.isLoading"
        :disabled="this.isFailed"

        @click="submitPayment"
    >
        Pay ${{ this.plan.price }}
    </v-btn>
</template>


<script>
import { mapGetters, mapActions } from 'vuex'

export default {
    name: 'payment-methods-pay-button',

    props: {
        paymentMethodId: {
            type: String,
            required: true,
        }
    },

    created() {
        this.loadPlan(this.$route.params.planSlug)
    },

    computed: {
        ...mapGetters({
            plan: 'plan/plan',
            loadStatuses: 'plan/loadStatuses',
            loadPlanStatus: 'plan/loadPlanStatus',
            updateSubscriptionStatus: 'plan/updateSubscriptionStatus',
        }),

        isLoading() { return this.isPlanLoading || this.isSubscriptionUpdating },
        isFailed() { return this.isPlanLoadingFailed || this.isSubscriptionUpdatingFailed },

        isPlanLoading() { return this.loadPlanStatus === this.loadStatuses.STARTED },
        isPlanLoaded() { return this.loadPlanStatus === this.loadStatuses.COMPLETED },
        isPlanLoadingFailed() { return this.loadPlanStatus === this.loadStatuses.FAILED },

        isSubscriptionUpdating() { return this.updateSubscriptionStatus === this.loadStatuses.STARTED },
        isSubscriptionUpdated() { return this.updateSubscriptionStatus === this.loadStatuses.COMPLETED },
        isSubscriptionUpdatingFailed() { return this.updateSubscriptionStatus === this.loadStatuses.FAILED },
    },

    methods: {
        ...mapActions({
            loadPlan: 'plan/fetchPlan',
            updateSubscription: 'plan/updateSubscription',
        }),

        submitPayment() {
            if (!this.paymentMethodId) {
                alert('Choose a payment method')
                return
            }

            if (!this.plan) {
                alert('Plan not available')
                return
            }

            this.updateSubscription({
                plan_slug: this.plan.slug,
                payment: this.paymentMethodId,
            })
        },
    },
}
</script>
