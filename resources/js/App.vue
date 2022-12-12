<template>
    <v-app>
        <my-app-bar></my-app-bar>

        <v-main>
            <v-container class="w-75 mx-auto">
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

        this.$store.dispatch('lecture/fetchAllLectures')
        this.$store.dispatch('conference/fetchAllConferences')
        this.$store.dispatch('category/fetchAllCategories')
    },
}
</script>

<style>

</style>
