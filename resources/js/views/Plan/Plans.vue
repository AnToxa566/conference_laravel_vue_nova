<template>
    <div>
        <custom-header>
            <template v-slot:header>Plans</template>
        </custom-header>

        <plan-cards-list></plan-cards-list>
    </div>
</template>


<script>
import PlanCardsList from "../../components/Plan/PlanCardsList.vue"

export default {
    name: 'plans',

    components: {
        PlanCardsList,
    },
}
</script>
