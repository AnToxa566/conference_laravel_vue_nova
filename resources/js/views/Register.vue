<template>
    <v-container class="h-100 fill-height">
        <v-layout class='align-center justify-center'>
            <v-flex class='login-form text-center'>
                <v-card class="pa-5" variant="tonal">
                    <v-card-title class="text-h4 mb-4 font-weight-bold"> Register </v-card-title>

                    <v-form action="" @submit="register(user)">
                        <v-text-field
                            v-model="user.first_name"
                            label="First name"
                            type="text"
                            :rules="firstNameRules"
                            variant="solo"
                            placeholder="Enter the first name"
                            required
                        ></v-text-field>

                        <v-text-field
                            v-model="user.last_name"
                            label="Last name"
                            type="text"
                            :rules="lastNameRules"
                            variant="solo"
                            placeholder="Enter the last name"
                            required
                        ></v-text-field>

                        <Datepicker
                            class="mb-6"
                            placeholder="Date of birthday"
                            dark="true"
                            v-model="user.birthdate"
                            modelType="yyyy-MM-dd"
                            :maxDate="new Date()"
                            :enableTimePicker="false"
                            required
                        />

                        <v-select
                            v-model="user.country"
                            :items="countries"
                            :rules="[v => !!v || 'Country is required!']"
                            variant="solo"
                            label="Country"
                            required
                        ></v-select>

                        <v-text-field
                            v-model="user.phone_number"
                            label="phone_number"
                            type="text"
                            :rules="phone_numberRules"
                            variant="solo"
                            placeholder="Enter phone_number number"
                            required
                        ></v-text-field>

                        <v-select
                            v-model="user.type"
                            :items="types"
                            :rules="[v => !!v || 'User type is required!']"
                            variant="solo"
                            label="Type"
                            required
                        ></v-select>

                        <v-text-field
                            v-model="user.email"
                            label="E-mail"
                            type="email"
                            :rules="emailRules"
                            variant="solo"
                            placeholder="Enter a email"
                            required
                        ></v-text-field>

                        <v-text-field
                            v-model="user.password"
                            label="Password"
                            type="password"
                            :rules="passwordRules"
                            variant="solo"
                            required
                        ></v-text-field>

                        <v-text-field
                            v-model="user.password_confirmation"
                            label="Confirm password"
                            type="password"
                            :rules="passwordConfirmRules"
                            variant="solo"
                            required
                        ></v-text-field>

                        <v-btn
                            type="submit"
                            variant="tonal"
                            color="white"
                            class="w-100"
                            @click.prevent="register(user)"
                        > Register </v-btn>
                    </v-form>
                </v-card>

                <div class="d-flex justify-center align-center mt-3">
                    <p class="mb-0 mx-2"> Already have an account? </p>

                    <router-link class="mx-2 text-decoration-none" to="/login">
                        <v-btn
                            variant="tonal"
                            color="white"
                        > Login </v-btn>
                    </router-link>
                </div>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
export default {
    name:'Register',
    data: () => ({
        user: {
            first_name: "",
            last_name: "",
            birthdate: "",
            phone_number: "",

            country: null,
            type: null,

            email: "",
            password: "",
            password_confirmation: "",
        },

        countries: ['Foo', 'Bar', 'Fizz', 'Buzz'],
        types: ['Listener', 'Speaker'],
    }),
    methods:{
        register(user) {
            axios.get("/sanctum/csrf-cookie").then(response => {
                console.log(response);
                this.$store.dispatch('auth/register', user)
            });
        },
    }
}
</script>

<style scoped>
    .login-form {
        min-width: 500px;
    }
</style>
