<template>
    <my-header>
        <template v-slot:header>Conferences</template>
    </my-header>

    <div class="d-flex">
        <div
            v-if="isAuthenticated"
            class="w-25 me-4"
        >
            <conference-filter-navigation></conference-filter-navigation>
        </div>

        <div class="w-100">
            <suspense>
                <template #default>
                    <conference-list></conference-list>
                </template>

                <template #fallback>
                    <conference-item-skeleton></conference-item-skeleton>
                </template>
            </suspense>
        </div>
    </div>
</template>


<script>
import ConferenceList from '../../components/Conference/ConferenceList.vue'

export default {
    components: {
        ConferenceList,
    },

    computed: {
        isAuthenticated() {
            return this.$store.getters['auth/authenticated']
        }
    },
}
</script>
