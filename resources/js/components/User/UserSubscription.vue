<template >
    <div>
        <custom-header>
            <template v-slot:header>Subscription</template>
        </custom-header>

        <div
            v-if="this.loadUserPlanStatus === this.loadStatuses.STARTED"
            class="d-flex justify-content-center">
            <v-progress-circular indeterminate></v-progress-circular>
        </div>

        <div v-else-if="this.loadUserPlanStatus === this.loadStatuses.COMPLETED">
            <user-subscription-info></user-subscription-info>

            <div class="d-flex justify-content-end">
                <user-subscription-button></user-subscription-button>
            </div>
        </div>
    </div>
</template>


<script>
import UserSubscriptionButton from './UserSubscriptionButton.vue'
import UserSubscriptionInfo from './UserSubscriptionInfo.vue'

import { mapGetters, mapActions } from 'vuex'

export default {
    name: 'user-subscription',

    components: {
        UserSubscriptionButton,
        UserSubscriptionInfo,
    },

    created() {
        this.loadUserPlan()
    },

    computed: {
        ...mapGetters({
            loadStatuses: 'plan/loadStatuses',
            loadUserPlanStatus: 'plan/loadUserPlanStatus',
        }),
    },

    methods: {
        ...mapActions({
            loadUserPlan: 'plan/fetchUserPlan',
        }),
    },
}
</script>
