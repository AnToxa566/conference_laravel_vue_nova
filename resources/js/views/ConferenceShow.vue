<template>
    <p class="text-h4 mb-4">Details</p>

    <my-info-card>
        <template v-slot:header> Topic </template>
        <template v-slot:body> Научная выставка </template>
    </my-info-card>

    <my-info-card>
        <template v-slot:header> Date / Time </template>
        <template v-slot:body> 13 окт. 2022 16:00 </template>
    </my-info-card>

    <my-info-card>
        <template v-slot:header> Address </template>
        <template v-slot:body> Запорожье, ул. Жуковского, д. 64, ауд. 316 </template>
        <template v-slot:map> <v-card id="map"></v-card> </template>
    </my-info-card>

    <my-info-card>
        <template v-slot:header> Country </template>
        <template v-slot:body> Украина </template>
    </my-info-card>

    <v-row>
        <v-col cols="2">
            <v-btn variant="tonal" color="white" class="w-100" @click="$router.go(-1)"> Back </v-btn>
        </v-col>
    </v-row>
</template>

<script>
import MyInfoCard from '../components/UI/MyInfoCard.vue'

export default {
    components: {
        MyInfoCard
    },
}
</script>

<style scoped>
    #map {
        height: 300px;
        margin-top: 8px;
    }
</style>
