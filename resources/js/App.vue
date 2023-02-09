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
import { mapGetters, mapActions } from 'vuex'

export default {
    name: 'app',

    components: {
        AppBar,
    },

    mounted() {
        if (this.authenticated) {
            this.initData()
            this.fetchUserData()

            this.fetchAllLectures()

            this.fetchMeetingsFromAPI()
            this.fetchMeetingsFromDB()
        }

        this.checkAuth()

        this.fetchAllConferences()
        this.fetchAllCountries()
        this.fetchAllCategories()
    },

    computed: {
        ...mapGetters({
            authenticated: 'auth/authenticated',
        }),
    },

    methods: {
        ...mapActions({
            initData: 'auth/initData',
            fetchUserData: 'auth/fetchUserData',

            fetchAllLectures: 'lecture/fetchAllLectures',

            fetchMeetingsFromAPI: 'meeting/fetchMeetingsFromAPI',
            fetchMeetingsFromDB: 'meeting/fetchMeetingsFromDB',

            checkAuth: 'auth/checkAuth',

            fetchAllConferences: 'conference/fetchAllConferences',
            fetchAllCountries: 'country/fetchAllCountries',
            fetchAllCategories: 'category/fetchAllCategories',
        }),
    },
}
</script>
