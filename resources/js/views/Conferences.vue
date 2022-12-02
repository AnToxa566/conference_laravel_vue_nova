<template>
    <my-header>
        <template v-slot:header>Conferences</template>
    </my-header>

    <conference-list></conference-list>
</template>

<script>
import ConferenceList from '../components/ConferenceList.vue'

export default {
  components: {
    ConferenceList,
},
}
</script>

<style scoped>

</style>
