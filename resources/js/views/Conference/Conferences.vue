<template>
    <my-header>
        <template v-slot:header>Conferences</template>
    </my-header>

    <v-row>
        <v-col cols="3">
            <conference-filter-navigation></conference-filter-navigation>
        </v-col>

        <v-col cols="9">
           <conference-list></conference-list>
        </v-col>
    </v-row>
</template>

<script>
import ConferenceList from '../../components/Conference/ConferenceList.vue'
import ConferenceFilterNavigation from '../../components/Filter/ConferenceFilterNavigation.vue';

export default {
    components: {
        ConferenceList,
        ConferenceFilterNavigation,
    },
}
</script>

<style scoped>

</style>
