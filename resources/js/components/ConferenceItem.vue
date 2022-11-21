<template>
  <v-card class="pa-4 mb-4" variant="tonal"  @click.stop="$router.push(`/conferences/${conference.id}`)">
    <v-card-title class="text-h5"> {{ conference.title }} </v-card-title>

    <v-card-subtitle> {{ this['conference/formatedDateTime'] }} </v-card-subtitle>
    <v-card-subtitle> {{ conference.country }} </v-card-subtitle>

    <v-card-actions>
      <v-btn variant="tonal" color="white" @click.stop="$router.push(`/`)"> Join </v-btn>
      <v-btn variant="tonal" color="white" @click.stop="$router.push(`/conferences/${conference.id}`)"> Details </v-btn>
      <v-btn variant="tonal" color="white" @click.stop="$router.push(`/conferences/${conference.id}/edit`)"> Update </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    props: {
        conference: {
            type: Object,
            required: true,
        },
    },

    computed: {
        ...mapGetters(["conference/formatedDateTime"]),
    }
};
</script>

<style scoped>
</style>
