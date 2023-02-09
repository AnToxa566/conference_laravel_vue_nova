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
import AppBar from './components/UI/AppBar.vue'
import { mapGetters } from 'vuex'

export default {
    name: 'app',

    components: {
        AppBar,
    },

    mounted() {
        if (this.authenticated) {
            this.$store.dispatch('auth/initData')
            this.$store.dispatch('auth/fetchUserData')

            this.$store.dispatch('lecture/fetchAllLectures')

            this.$store.dispatch('meeting/fetchMeetingsFromAPI')
            this.$store.dispatch('meeting/fetchMeetingsFromDB')
        }

        this.$store.dispatch('auth/checkAuth')

        this.$store.dispatch('conference/fetchAllConferences')
        this.$store.dispatch('country/fetchAllCountries')
        this.$store.dispatch('category/fetchAllCategories')
    },

    computed: {
        ...mapGetters({
            authenticated: 'auth/authenticated',
        }),
    }
}
</script>
