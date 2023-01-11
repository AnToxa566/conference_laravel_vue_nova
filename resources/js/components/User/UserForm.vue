<template>
    <v-form
        v-model="valid"
        ref="form"
        @submit="onSubmit(user)"
        action=""
    >

        <!-- First name -->

        <custom-input
            v-model="user.first_name"
            :rules="[required]"

            label="First name"
            type="text"
        >
        </custom-input>

        <!-- Last name -->

        <custom-input
            v-model="user.last_name"
            :rules="[required]"

            label="Last name"
            type="text"
        >
        </custom-input>

        <!-- Date of birthday -->

        <custom-datepicker
            v-model="user.birthdate"
            placeholder="Date of birthday"

            :max="new Date()"
        >
        </custom-datepicker>

        <!-- Country -->

        <country-selected
            v-model="user.country"
            :rules="[v => !!v || 'Country is required!']"
        >
        </country-selected>

        <!-- Phone number -->

        <custom-phone-number-input
            v-model="user.phone_number"
            :modelValue="user.phone_number"
            :countryCode="user.country_phone_code"

            @updateValue="phoneUpdated"
        >
        </custom-phone-number-input>

        <!-- E-mail -->

        <custom-input
            v-model="user.email"
            :rules="[
                v => !!v || 'Email is required!',
                v => /.+@.+/.test(v) || 'E-mail must be valid',
            ]"

            label="E-mail"
            type="email"

            class="mt-6"
        ></custom-input>

        <!-- Password -->

        <custom-input
            v-model="user.password"
            :rules="[
                v => !!v || 'Password is required!',
                v => (v && v.length >= 8) || 'Password must be 8 characters or longer!',
            ]"

            label="Password"
            type="password"
        ></custom-input>

        <!-- Confirm password -->

        <custom-input
            v-model="user.password_confirmation"
            :rules="[v => !!v || 'Confirm password is required!', matchingPasswords]"

            label="Confirm password"
            type="password"
        ></custom-input>

        <!-- Submit button -->

        <v-btn
            type="submit"
            variant="tonal"
            color="white"
            class=""
            @click.prevent="onSubmit(user)"
        >
            Save
        </v-btn>
    </v-form>
</template>


<script>
import { computed } from 'vue'
import { useStore } from 'vuex'

export default {
    name:'UserForm',

    setup () {
        const store = useStore()

        const isAuthenticated = computed(() => store.getters['auth/authenticated'])
        const storeUser = computed(() => store.getters['auth/user'])

        return {
            isAuthenticated,
            storeUser,
        }
    },

    data: () => ({
        valid: false,
        phoneValid: null,

        user: {
            first_name: '',
            last_name: '',

            birthdate: '',
            country: '',

            phone_number: '',
            country_phone_code: '',
            email: '',

            password: '',
            password_confirmation: '',
        },
    }),

    created() {
        if (this.isAuthenticated) Object.assign(this.user, this.storeUser);
    },

    methods: {
        required (v) {
            return !!v || 'Field is required'
        },

        matchingPasswords () {
            return this.user.password == this.user.password_confirmation || 'Passwords does not match!'
        },

        phoneUpdated(event) {
            this.phoneValid = event
        },

        async onSubmit(user) {
            const { valid } = await this.$refs.form.validate()

            if (valid) {
                this.$emit('submit', user)
            }
        },
    },
}
</script>
