<template>
    <v-btn
        variant="tonal"
        color="white"
        @click="this.isPlanBasic ? this.upgradeSubscription() : this.cancelSubscription()"
    >
        {{ this.isPlanBasic ? this.buttonsText.UPGRADE_SUB : this.buttonsText.CANCEL_SUB }}
    </v-btn>
</template>


<script>
import buttons_text from '../../config/buttons_text'
import { mapGetters } from 'vuex'

export default {
    name: 'user-subscription-button',

    data: () => ({
        buttonsText: buttons_text,
    }),

    computed: {
        ...mapGetters({
            plan: 'plan/currentPlan',
            planSlugs: 'plan/planSlugs',
        }),

        isPlanBasic() {
            return this.plan.slug === this.planSlugs.BASIC
        },
    },

    methods: {
        upgradeSubscription() {
            this.$router.push({ name: 'plans' })
        },

        cancelSubscription() {
            console.log('cancelSubscription')
        },
    },
}
</script>
