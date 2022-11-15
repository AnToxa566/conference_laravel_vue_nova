<template>
  <v-card class="pa-4 mb-4" variant="tonal"  @click.stop="$router.push(`/conferences/${1}`)">
    <v-card-title class="text-h5"> Научная выставка </v-card-title>

    <v-card-subtitle> 13 октября в 16:00 (четверг) </v-card-subtitle>
    <v-card-subtitle> Запорожье, Украина </v-card-subtitle>

    <v-card-actions>
      <v-btn variant="tonal" color="white" @click.stop="$router.push(`/`)"> Join </v-btn>
      <v-btn variant="tonal" color="white" @click.stop="$router.push(`/conferences/${1}`)"> Details </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {

};
</script>

<style scoped>
</style>
