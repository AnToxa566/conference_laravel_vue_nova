<template>
    <v-app>
        <app-bar></app-bar>

        <v-main>
            <v-container class="w-75 mx-auto">
                <bread-crumb></bread-crumb>

                <router-view></router-view>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
import AppBar from './components/UI/AppBar.vue';

export default {
    name: 'app',

    components: {
        AppBar,
    },

    mounted() {
        this.$store.dispatch('auth/initData')
        this.$store.dispatch('auth/fetchUserData')

        this.$store.dispatch('lecture/fetchAllLectures')
        this.$store.dispatch('conference/fetchAllConferences')
        this.$store.dispatch('country/fetchAllCountries')
        this.$store.dispatch('category/fetchAllCategories')
    },
}
</script>

<style>

</style>
