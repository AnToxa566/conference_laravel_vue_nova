<template>
    <v-app>
        <my-app-bar></my-app-bar>

        <v-main>
            <v-container class="w-75 mx-auto">
                <bread-crumb></bread-crumb>

                <router-view></router-view>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
import MyAppBar from './components/UI/MyAppBar.vue';

export default {
    name: 'app',

    components: {
        MyAppBar,
    },

    mounted() {
        this.$store.dispatch('auth/initData')
        this.$store.dispatch('auth/fetchUserData')

        this.$store.dispatch('lecture/fetchAllLectures')
        this.$store.dispatch('conference/fetchAllConferences')
        this.$store.dispatch('conference/fetchAllCountries')
        this.$store.dispatch('category/fetchAllCategories')
    },
}
</script>

<style>

</style>
