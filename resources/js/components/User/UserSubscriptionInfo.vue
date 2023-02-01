<template>
    <div>
        <info-card>
            <template v-slot:header> Current Plan </template>
            <template v-slot:body> {{ this.plan.slug }} </template>
        </info-card>

        <info-card v-show="this.plan.slug !== this.planSlugs.UNLIMITED">
            <template v-slot:header> Left Joins </template>
            <template v-slot:body> {{ this.user.joins_left }} </template>
        </info-card>
    </div>
</template>


<script>
import { mapGetters } from 'vuex'

export default {
    name: 'user-subscription-info',

    computed: {
        ...mapGetters({
            plan: 'plan/currentPlan',
            user: 'auth/user',
            planSlugs: 'plan/planSlugs',
        }),
    },
}
</script>
