<template>
    <v-app>
        <my-app-bar></my-app-bar>

        <v-main>
            <v-container>
                <router-view></router-view>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
import MyAppBar from './components/UI/MyAppBar.vue';

export default {
    components: {
        MyAppBar,
    }
}
</script>

<style>

</style>
