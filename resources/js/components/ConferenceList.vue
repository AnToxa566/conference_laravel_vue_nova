<template>
    <conference-item
        v-for="conference in this['conference/conferences']"
        :key="conference.id"
        :conference="conference"
    >
    </conference-item>
</template>

<script>
import ConferenceItem from './ConferenceItem.vue'
import { mapGetters } from "vuex";

export default {
    components: {
        ConferenceItem,
    },

    computed: {
        ...mapGetters(["conference/conferences"])
    },

    mounted() {
        this.$store.dispatch('conference/fetchAllConferences')
    }
}
</script>

<style scoped>

</style>
