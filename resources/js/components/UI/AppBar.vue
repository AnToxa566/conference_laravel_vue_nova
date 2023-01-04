<template>
    <v-app-bar class="px-15" app elevate-on-scroll>

        <!-- Prepend Slot -->

        <template v-slot:prepend>
            <v-app-bar-title>
                <router-link to="/" class="text-decoration-none text-white font-weight-bold"> CONFY </router-link>
            </v-app-bar-title>
        </template>

        <!-- Append Slot -->

        <template v-slot:append>

            <!-- App bar when user is authorized -->

            <div
                v-if="this.isAuthenticated"
                class="d-flex align-center"
            >
                <!-- Search Input -->
                <app-bar-search-input></app-bar-search-input>

                <!-- Dropdown menu -->
                <dropdown-menu></dropdown-menu>

                <!-- Button leading to the page with favorited lectures -->
                <app-bar-favorite></app-bar-favorite>
            </div>

            <!-- App bar when user isn't authorized -->

            <div
                v-else
            >
                <router-link to="/login" class="mx-2 text-decoration-none text-white"> Login </router-link>
                <router-link to="/register" class="mx-2 text-decoration-none text-white"> Register </router-link>
            </div>
        </template>
    </v-app-bar>
</template>

<script>
export default {
    name: 'app-bar',

    computed: {
        isAuthenticated() {
            return this.$store.state.auth.authenticated
        },
    },
}
</script>
