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

                    <span class="text-h5 mx-2">|</span>

                    <router-link to="/favorite" class="text-decoration-none mx-2">
                        <v-badge
                            v-if="hasFavoritedLectures"
                            :content="badgeContent"
                            color="error"
                        >
                            <v-icon size="x-large" color="white">
                                {{ this.heartFull }}
                            </v-icon>
                        </v-badge>
                        <v-icon
                            v-else
                            size="x-large" color="white"
                        >
                            {{ this.heartOutline }}
                        </v-icon>
                    </router-link>
                </div>
            </v-sheet>
            <v-sheet v-else>
                <router-link to="/login" class="mx-2 text-decoration-none text-white"> Login </router-link>

                <span class="text-h5 mx-2">|</span>

                <router-link to="/register" class="mx-2 text-decoration-none text-white"> Register </router-link>
            </v-sheet>
        </template>
    </v-app-bar>
</template>

<script>
export default {
    name: 'my-app-bar',

    data: () => ({
        heartOutline: 'mdi-cards-heart-outline',
        heartFull: 'mdi-cards-heart',
    }),

    computed: {
        isAdmin() {
            return this.$store.getters['auth/isAdmin']
        },

        badgeContent() {
            return this.$store.getters['favorite/favoritedLecturesId'].length > 99 ? '99+' : this.$store.getters['favorite/favoritedLecturesId'].length
        },

        hasFavoritedLectures() {
            return this.$store.getters['favorite/hasFavoritedLectures']
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
