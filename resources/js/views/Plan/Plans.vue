<template>
    <div>
        <custom-header>
            <template v-slot:header>Plans</template>
        </custom-header>

        <plan-cards-list></plan-cards-list>

        <joins-ended-flash-message
            v-if="this.isUserPlanLoaded"
            v-model="this.flashMessage"
            :text="`You have reached your limit on ${this.currentPlan.slug} plan conference joins this month!`"
        ></joins-ended-flash-message>
    </div>
</template>


<script>
import PlanCardsList from "../../components/Plan/PlanCardsList.vue"
import JoinsEndedFlashMessage from "../../components/FlashMessages/JoinsEndedFlashMessage.vue"

import { mapGetters, mapActions } from "vuex"

export default {
    name: 'plans',

    components: {
        PlanCardsList,
        JoinsEndedFlashMessage,
    },

    data: () => ({
        flashMessage: false,
    }),

    created() {
        this.fetchUserPlan()
    },

    mounted() {
        if (this.$route.query.joinsEnded) {
            this.flashMessage = true
        }
    },

    computed: {
        ...mapGetters({
            currentPlan: 'plan/currentPlan',
            loadStatuses: 'plan/loadStatuses',
            loadUserPlanStatus: 'plan/loadUserPlanStatus',
        }),

        isUserPlanLoaded() {
            return this.loadUserPlanStatus === this.loadStatuses.COMPLETED
        },
    },

    methods: {
        ...mapActions({
            fetchUserPlan: 'plan/fetchUserPlan',
        }),
    },
}
</script>
