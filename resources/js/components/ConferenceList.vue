<template>
    <conference-item
        v-for="i in 10"
        :key="i"
    >

    </conference-item>
</template>

<script>
import ConferenceItem from './ConferenceItem.vue'

export default {
    components: {
        ConferenceItem,
    }
}
</script>

<style scoped>

</style>
