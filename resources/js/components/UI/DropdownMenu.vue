<template>
    <v-menu
        open-on-hover
        class="me-2"
    >
        <!-- Dropdown Activator -->

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

        <!-- Dropdown List -->

        <v-list>
            <!-- User Name -->

            <v-list-subheader
                class="font-weight-bold text-white"
            >
                {{ this.getUserFullName }}
            </v-list-subheader>


            <!-- User's Profile Page -->

            <v-list-item
                value="true"
                prepend-icon="mdi-account-outline"
                @click="this.$router.push('/profile')"
            >
                Profile
            </v-list-item>


            <!-- User's Profile Page -->

            <v-list-item
                value="true"
                prepend-icon="mdi-credit-card-outline"
                @click="this.$router.push({ name: 'plans' })"
            >
                Products
            </v-list-item>


            <!-- Logout Button -->

            <v-list-item
                value="true"
                prepend-icon="mdi-exit-to-app"
                @click="logout()"
            >
                Logout
            </v-list-item>
        </v-list>
    </v-menu>
</template>


<script>
export default {
    name: 'dropdown-menu',

    computed: {
        getUserFullName() {
            return this.$store.getters['auth/user'].first_name + ' ' + this.$store.getters['auth/user'].last_name
        },
    },

    methods: {
        logout() {
            axios.get("/sanctum/csrf-cookie").then(response => {
                this.$store.dispatch('auth/logout')
            });
        },
    },
}
</script>
