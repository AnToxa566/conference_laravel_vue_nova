<template>
    <custom-header>
        <template v-slot:header>Meetings</template>
    </custom-header>

    <meetings-table></meetings-table>
</template>


<script>
import MeetingsTable from '../../components/Meeting/MeetingsTable.vue'

export default {
    components: {
        MeetingsTable,
    },

    mounted() {
        this.$store.dispatch('meeting/fetchMeetingsFromAPI')
        this.$store.dispatch('meeting/fetchMeetingsFromDB')
    },
}
</script>
