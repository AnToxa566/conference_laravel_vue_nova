<template>
    <v-app-bar class="px-15" app elevate-on-scroll>
        <template v-slot:prepend>
            <v-app-bar-title>
                <router-link to="/" class="text-decoration-none text-white font-weight-bold"> CONFY </router-link>
            </v-app-bar-title>
        </template>

        <template v-slot:append>
            <router-link to="/conferences/add" class="text-decoration-none text-white">Add</router-link>
        </template>
    </v-app-bar>
</template>

<script>
    export default {

    }
</script>

<style>

</style>
