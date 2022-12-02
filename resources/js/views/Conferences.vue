<template>
    <div
        class="py-3 text-h4 font-weight-bold"
    >
        Conferences
    </div>

    <conference-list></conference-list>
</template>

<script>
import ConferenceList from '../components/ConferenceList.vue'

export default {
  components: { ConferenceList },
}
</script>

<style scoped>

</style>
