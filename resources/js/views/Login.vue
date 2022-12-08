<template>
    <v-container class="h-100 fill-height">
        <v-layout class='align-center justify-center'>
            <v-flex class='login-form text-center'>
                <v-card class="pa-5" variant="tonal">

                    <v-card-title class="text-h4 mb-4 font-weight-bold"> Login </v-card-title>

                    <v-alert
                        v-if="hasAuthErrors"
                        type="error"
                        variant="tonal"
                        class="mb-6"
                    >
                        {{ this.authErrors }}
                    </v-alert>

                    <v-form
                        action=""
                        @submit="login(user)"
                        ref="form"
                        v-model="valid"
                    >
                        <v-text-field
                            v-model="user.email"
                            label="E-mail"
                            type="email"
                            :rules="[
                                v => !!v || 'Email is required!',
                                v => /.+@.+/.test(v) || 'E-mail must be valid',
                            ]"
                            variant="solo"
                            placeholder="Enter a email"
                            required
                        ></v-text-field>

                        <v-text-field
                            v-model="user.password"
                            label="Password"
                            type="password"
                            :rules="[
                                v => !!v || 'Password is required!',
                                v => (v && v.length >= 8) || 'Password must be 8 characters or longer!',
                            ]"
                            variant="solo"
                            required
                        ></v-text-field>

                        <v-btn
                            variant="tonal"
                            color="white"
                            class="w-100"
                            @click.prevent="login(user)"
                        > Login </v-btn>
                    </v-form>
                </v-card>

                <div class="d-flex justify-center align-center mt-3">
                    <p class="mb-0 mx-2"> Don't have an account? </p>

                    <router-link class="mx-2 text-decoration-none" to="/register">
                        <v-btn
                            variant="tonal"
                            color="white"
                        > Register </v-btn>
                    </router-link>
                </div>
            </v-flex>
        </v-layout>
    </v-container>
</template>


<script>
import axios from 'axios';

export default {
    name: "Login",

    data() {
        return {
            valid: false,

            user: {
                email: "",
                password: ""
            },
        }
    },

    computed: {
        hasAuthErrors() {
            return this.$store.getters['auth/hasAuthErrors']
        },
        authErrors() {
            return this.$store.getters['auth/authErrors']
        },
    },

    created() {
        this.$store.dispatch('auth/removeAuthErrors')
    },

    methods: {
        async login(user) {
            const { valid } = await this.$refs.form.validate()

            if (valid) {
                axios.get("/sanctum/csrf-cookie").then(response => {
                    this.$store.dispatch('auth/login', user)
                });
            }
        },
    }
}
</script>


<style scoped>
    .login-form {
        min-width: 500px;
    }
</style>
