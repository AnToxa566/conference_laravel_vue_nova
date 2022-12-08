<template>
    <v-app-bar class="px-15" app elevate-on-scroll>

        <template v-slot:prepend>
            <v-app-bar-title>
                <router-link to="/" class="text-decoration-none text-white font-weight-bold"> CONFY </router-link>
            </v-app-bar-title>
        </template>

        <template v-slot:append>
            <v-sheet
                v-if="this.isAuthenticated"
            >
                <div class="d-flex align-center">
                    <v-menu
                        open-on-hover
                        class="me-2"
                    >
                        <template v-slot:activator="{ props }">
                            <v-btn
                                color="white"
                                v-bind="props"
                            >
                                <v-icon
                                    size="x-large"
                                    class="me-2"
                                >
                                    mdi-account
                                </v-icon>

                                Your account
                            </v-btn>
                        </template>

                        <v-list>
                            <v-list-subheader>
                                {{ this.getUserFullName }}
                            </v-list-subheader>

                            <v-list-item
                                v-if="this.isAdmin"
                                value="true"
                                prepend-icon="mdi-plus"
                                @click="this.$router.push('/conferences/add')"
                            >
                                Add conference
                            </v-list-item>

                            <v-list-item
                                value="true"
                                prepend-icon="mdi-account-outline"
                                @click="this.$router.push('/profile')"
                            >
                                Profile
                            </v-list-item>

                            <v-list-item
                                value="true"
                                prepend-icon="mdi-exit-to-app"
                                @click="logout()"
                            >
                                Logout
                            </v-list-item>
                        </v-list>
                    </v-menu>

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

            <v-sheet
                v-else
            >
                <router-link to="/login" class="mx-2 text-decoration-none text-white"> Login </router-link>
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

        isAuthenticated() {
            return this.$store.state.auth.authenticated
        },

        getUserFullName() {
            return this.$store.getters['auth/user'].first_name + ' ' + this.$store.getters['auth/user'].last_name
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
        },
    },
}
</script>

<style scoped>

</style>
