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

            <!-- Add Conference Page -->

            <v-list-item
                v-if="this.isAdmin"
                value="true"
                prepend-icon="mdi-monitor-account"
                @click="this.$router.push('/conferences/add')"
            >
                Add conference
            </v-list-item>

            <!-- Categories Edit Page -->

            <v-list-item
                v-if="this.isAdmin"
                value="true"
                prepend-icon="mdi-tag-outline"
                @click="this.$router.push('/categories')"
            >
                Categories
            </v-list-item>

            <!-- User's Profile Page -->

            <v-list-item
                value="true"
                prepend-icon="mdi-account-outline"
                @click="this.$router.push('/profile')"
            >
                Profile
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
        isAdmin() {
            return this.$store.getters['auth/isAdmin']
        },

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
