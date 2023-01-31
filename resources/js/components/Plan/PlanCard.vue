<template>
    <div>
        <v-card class="h-100" variant="tonal">
            <div
                class="py-8"
                :class="this.isCurrentPlan ? 'bg-green-darken-3' : ''"
            >
                <v-card-title class="text-center text-capitalize font-weight-bold">
                    {{ this.plan.slug }}
                </v-card-title>

                <v-card-subtitle class="text-center">
                    Choose your plane wisely
                </v-card-subtitle>
            </div>

            <div class="h-100 text-center bg-grey-darken-4">
                <div class="pt-10">
                    <span class="font-weight-bold">{{ this.plan.joins === -1 ? 'Unlimited' : this.plan.joins }} {{ this.plan.joins === 1 ? 'join' : 'joins' }}</span>
                </div>

                <div class="py-10">
                    <p class="text-green-darken-2">
                        <span class="text-h4 font-weight-bold">${{ this.plan.price }}/</span><span>Month</span>
                    </p>

                    <v-btn
                        v-show="this.plan.price && !this.isCurrentPlan"
                        color="green-darken-2"
                        @click="upgradePlan"
                    >
                        Upgrade
                    </v-btn>

                    <p
                        v-show="this.isCurrentPlan"
                        class="font-weight-bold"
                    >
                        Active plan
                    </p>
                </div>
            </div>
        </v-card>
    </div>
</template>


<script>
import { mapGetters, mapActions } from 'vuex'

export default {
    name: 'plan-card',

    created() {
        this.loadUserPlan()
    },

    props: {
        plan: {
            type: Object,
            required: true,
        }
    },

    computed: {
        ...mapGetters({
            currentPlan: 'plan/currentPlan',
        }),

        isCurrentPlan() {
            return this.plan.slug === this.currentPlan.slug
        },
    },

    methods: {
        ...mapActions({
            loadUserPlan: 'plan/fetchUserPlan',
        }),

        upgradePlan() {
            this.$router.push({ name: 'payment', params: { planSlug: this.plan.slug }})
        },
    },
}
</script>
