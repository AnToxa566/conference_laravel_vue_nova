<template>
    <p class="text-h4 mb-4">Details</p>

    <my-info-card>
        <template v-slot:header> Topic </template>
        <template v-slot:body> {{ this.conference.title }} </template>
    </my-info-card>

    <my-info-card>
        <template v-slot:header> Date / Time </template>
        <template v-slot:body> {{ this.formatedDateTime }} </template>
    </my-info-card>

    <my-info-card>
        <template v-slot:header> Address </template>
        <template v-slot:body> TODO </template>
    </my-info-card>

    <my-map></my-map>

    <my-info-card>
        <template v-slot:header> Country </template>
        <template v-slot:body> {{ this.conference.country }} </template>
    </my-info-card>

    <v-row>
        <v-col cols="2">
            <v-btn variant="tonal" color="white" class="w-100" @click="$router.go(-1)"> Back </v-btn>
        </v-col>
        <v-col cols="2">
            <v-btn variant="tonal" color="white" class="w-100" @click="$router.go(-1)"> Join </v-btn>
        </v-col>
        <v-col cols="2">
            <v-btn variant="tonal" color="red" class="w-100" @click="this.delete"> Delete </v-btn>
        </v-col>
    </v-row>
</template>

<script>
import MyInfoCard from '../components/UI/MyInfoCard.vue'
import MyMap from '../components/UI/MyMap.vue'
import { mapGetters } from "vuex";

export default {
    components: {
        MyInfoCard,
        MyMap,
    },

    data() {
        return {
            id: null,
        };
    },

    computed: {
        conference() {
            return this.$store.getters['conference/conference']
        },
        formatedDateTime() {
            return this.$store.getters['conference/formatedDateTime'](this.conference.id)
        },
    },

    mounted() {
        this.id = this.$route.params.id;
        this.$store.dispatch('conference/fetchDetailConference', this.id)
    },

    methods: {
        delete() {
            this.$store.dispatch('conference/deleteConference', this.id)
        },
    },
}
</script>

<style scoped>

</style>
