<template>
    <v-app-bar class="px-15" app elevate-on-scroll>
        <template v-slot:prepend>
            <v-app-bar-title>
                <router-link to="/" class="text-decoration-none text-white font-weight-bold"> CONFY </router-link>
            </v-app-bar-title>
        </template>

        <template v-slot:append>
            <v-sheet v-if="this.$store.state.auth.authenticated">
                <div class="d-flex align-center">
                    <router-link v-if="this.isAdmin" to="/conferences/add" class="mx-2 text-decoration-none text-white"> Add </router-link>
                    <p class="mb-0 mx-2 text-decoration-none text-white" @click="logout()" style="cursor: pointer;"> Logout </p>
                </div>
            </v-sheet>
            <v-sheet v-else>
                <router-link to="/login" class="mx-2 text-decoration-none text-white"> Login </router-link>
                <router-link to="/register" class="mx-2 text-decoration-none text-white"> Register </router-link>
            </v-sheet>
        </template>
    </v-app-bar>
</template>

<script>
export default {
    name: 'my-app-bar',

    computed: {
        isAdmin() {
            return this.$store.getters['auth/user'].type === 'admin'
        },
    },

    methods: {
        logout() {
            axios.get("/sanctum/csrf-cookie").then(response => {
                this.$store.dispatch('user_conferences/removeJoinedConferences')
                this.$store.dispatch('auth/logout')
            });
        }
    },
}
</script>

<style scoped>

</style>
