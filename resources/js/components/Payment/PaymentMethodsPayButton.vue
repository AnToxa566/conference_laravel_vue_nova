<template>
    <v-btn
        variant="tonal"
        color="white"

        :loading="this.isLoading"
        :disabled="this.isFailed || this.isPlanSubscribed"

        @click="submitPayment"
    >
        Pay ${{ this.plan.price }}
    </v-btn>
</template>


<script>
import { mapGetters, mapActions, mapMutations } from 'vuex'

export default {
    name: 'payment-methods-pay-button',

    props: {
        paymentMethodId: {
            type: String,
            required: true,
        }
    },

    created() {
        this.loadUserPlan()
        this.loadPlan(this.$route.params.planSlug)

        this.storeUpdateSubscriptionStatus(this.loadStatuses.NOT_STARTED)
    },

    computed: {
        ...mapGetters({
            plan: 'plan/plan',
            userPlan: 'plan/currentPlan',

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

        isPlanSubscribed() {
            return this.plan.slug === this.userPlan.slug
        },
    },

    methods: {
        ...mapActions({
            loadPlan: 'plan/fetchPlan',
            loadUserPlan: 'plan/fetchUserPlan',

            updateSubscription: 'plan/updateSubscription',
        }),

        ...mapMutations({
            storeUpdateSubscriptionStatus: 'plan/storeUpdateSubscriptionStatus',
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
                payment_method: this.paymentMethodId,
            })
        },
    },
}
</script>
