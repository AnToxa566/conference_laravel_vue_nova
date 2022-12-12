<template>
    <v-container class="h-100 fill-height">
        <v-layout class='align-center justify-center'>
            <v-flex class='login-form text-center'>
                <v-card class="pa-5" variant="tonal">

                    <v-card-title class="text-h4 mb-4 font-weight-bold"> Register </v-card-title>

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
                        @submit="register(user)"
                        ref="form"
                        v-model="valid"
                    >

                        <!-- First name -->

                        <v-text-field
                            v-model="user.first_name"
                            label="First name"
                            type="text"
                            :rules="[v => !!v || 'First name is required!']"
                            variant="solo"
                            placeholder="Enter the first name"
                            autofocus
                            required
                        ></v-text-field>

                        <!-- Last name -->

                        <v-text-field
                            v-model="user.last_name"
                            label="Last name"
                            type="text"
                            :rules="[v => !!v || 'Last name is required!']"
                            variant="solo"
                            placeholder="Enter the last name"
                            required
                        ></v-text-field>

                        <!-- Date of birthday -->

                        <Datepicker
                            placeholder="Date of birthday"
                            dark
                            v-model="user.birthdate"
                            modelType="yyyy-MM-dd"
                            :maxDate="new Date()"
                            :enableTimePicker="false"
                            @update:modelValue="hiddenMessage"
                            required
                        />

                        <div id="message__wrapper" class="hidden__message">
                            <p class="message">Date of birthday is required!</p>
                        </div>

                        <!-- Country -->

                        <country-selected
                            v-model="user.country"
                            :rules="[v => !!v || 'Country is required!']"
                        >
                        </country-selected>

                        <!-- Phone number -->

                        <MazPhoneNumberInput
                            v-model="user.phone_number"
                            :default-country-code="this.user.country_phone_code"

                            :preferred-countries="['UA']"
                            :success="phone_results?.isValid"

                            @update="phone_results = $event"
                            @country-code="countryCodeChange"
                        />

                        <!-- User type -->

                        <v-select
                            class="mt-5"
                            v-model="user.type"
                            :items="this.userTypes"
                            :rules="[v => !!v || 'User type is required!']"
                            variant="solo"
                            label="Type"
                            required
                        ></v-select>

                        <!-- E-mail -->

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

                        <!-- Password -->

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

                        <!-- Confirm password -->

                        <v-text-field
                            v-model="user.password_confirmation"
                            label="Confirm password"
                            type="password"
                            :rules="[v => !!v || 'Confirm password is required!', matchingPasswords]"
                            variant="solo"
                            required
                        ></v-text-field>

                        <!-- Submit button -->

                        <v-btn
                            type="submit"
                            variant="tonal"
                            color="white"
                            class="w-100"
                            @click.prevent="register(user)"
                        > Register </v-btn>
                    </v-form>
                </v-card>

                <!-- Authorization path -->

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
import MazPhoneNumberInput from 'maz-ui/components/MazPhoneNumberInput'

export default {
    name:'Register',

    components: {
        MazPhoneNumberInput,
    },

    data: () => ({
        valid: false,

        phone_results: null,

        user: {
            first_name: '',
            last_name: '',
            birthdate: '',

            phone_number: '',
            country_phone_code: '',

            country: null,
            type: null,

            email: '',
            password: '',
            password_confirmation: '',
        },

        countries: [],
    }),

    computed: {
        userTypes() {
            return this.$store.getters['auth/userTypes']
        },

        hasAuthErrors() {
            return this.$store.getters['auth/hasAuthErrors']
        },
        authErrors() {
            return this.$store.getters['auth/authErrors']
        },
    },

    created() {
        this.$store.dispatch('auth/removeAuthErrors')

        this.countries = this.$store.getters['conference/countriesName']
        this.user.country_phone_code ='UA'
    },

    methods: {
        matchingPasswords: function() {
            if (this.user.password === this.user.password_confirmation) {
                return true;
            } else {
                return 'Passwords does not match!';
            }
        },

        containsError: function(property) {
            if (this.registerErrors.hasOwnProperty(property)) {
                return this.registerErrors[property][0]
            } else {
                return true
            }
        },

        hiddenMessage() {
            if (this.user.birthdate === null || this.user.birthdate === '') {
                let message = document.getElementById("message__wrapper")
                message.classList.remove("hidden__message")
            }
            else {
                let message = document.getElementById("message__wrapper")
                message.classList.add("hidden__message")
            }
        },

        countryCodeChange(event) {
            this.user.country_phone_code = event
        },

        async register(user) {
            if (this.user.birthdate === '' || this.user.birthdate === null) {
                let message = document.getElementById("message__wrapper")
                message.classList.remove("hidden__message")
            }

            const { valid } = await this.$refs.form.validate()

            if (valid && this.phone_results?.isValid) {
                axios.get("/sanctum/csrf-cookie").then(response => {
                    this.$store.dispatch('auth/register', user)
                })
            }

            if (this.authErrors.hasOwnProperty('email')) {
                this.user.email = this.registerErrors['email'][0]
            }
        },
    }
}
</script>


<style scoped>
    .login-form {
        min-width: 500px;
    }
    .hidden__message {
        visibility: hidden;
    }
    .message {
        color: #cf6679;
        font-size: 12px;
        padding: 6px 16px 0 16px;
        margin-bottom: 0px;
    }
</style>
