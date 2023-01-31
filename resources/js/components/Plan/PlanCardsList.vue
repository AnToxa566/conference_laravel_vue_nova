<template>
    <div>
        <div
            v-if="this.loadPlansStatus === this.loadStatuses.STARTED"
            class="d-flex justify-content-center">
            <v-progress-circular indeterminate></v-progress-circular>
        </div>

        <div
            v-else-if="this.loadPlansStatus === this.loadStatuses.COMPLETED"
            class="d-flex justify-content-between"
        >
            <plan-card
                v-for="plan in this.sortedPlans"
                :key="plan.slug"
                :plan="plan"
                class="w-100 mx-4"
            ></plan-card>
        </div>
    </div>
</template>


<script>
import PlanCard from "../../components/Plan/PlanCard.vue"
import { mapGetters, mapActions } from 'vuex'

export default {
    name: 'plan-cards-list',

    components: {
        PlanCard,
    },

    created() {
        this.loadPlans()
    },

    computed: {
        ...mapGetters({
            plans: 'plan/plans',
            loadStatuses: 'plan/loadStatuses',
            loadPlansStatus: 'plan/loadPlansStatus',
        }),

        sortedPlans() {
            return this.plans.sort((a, b) => (a.slug > b.slug) ? 1 : ((b.slug > a.slug) ? -1 : 0))
        },
    },

    methods: {
        ...mapActions({
            loadPlans: 'plan/fetchPlans',
        }),
    },
}
</script>
