<template>
    <v-btn
        variant="tonal"
        color="white"
        :loading="this.isSubscriptionCanceling"
        @click="this.isPlanBasic ? this.upgradeSubscription() : this.cancelSubscription()"
    >
        {{ this.isPlanBasic ? this.buttonsText.UPGRADE_SUB : this.buttonsText.CANCEL_SUB }}
    </v-btn>
</template>


<script>
import buttons_text from '../../config/buttons_text'
import { mapGetters, mapActions } from 'vuex'

export default {
    name: 'user-subscription-button',

    data: () => ({
        buttonsText: buttons_text,
    }),

    computed: {
        ...mapGetters({
            plan: 'plan/currentPlan',

            planSlugs: 'plan/planSlugs',
            loadStatuses: 'plan/loadStatuses',

            cancelSubscriptionStatus: 'plan/cancelSubscriptionStatus',
        }),

        isPlanBasic() {
            return this.plan.slug === this.planSlugs.BASIC
        },

        isSubscriptionCanceling() {
            return this.cancelSubscriptionStatus === this.loadStatuses.STARTED
        },
    },

    methods: {
        ...mapActions({
            cancelSubscription: 'plan/cancelSubscription',
        }),

        upgradeSubscription() {
            this.$router.push({ name: 'plans' })
        },
    },
}
</script>
