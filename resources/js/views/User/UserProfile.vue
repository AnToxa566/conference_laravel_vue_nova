<template>
    <custom-header>
        <template v-slot:header>Profile</template>
    </custom-header>

    <custom-error-alert
        :errorMessage='this.authErrors'
        class="mb-6"
    >
    </custom-error-alert>

    <user-form
        @submit="updateUser"
    ></user-form>

    <user-subscription
        class="mt-4"
    ></user-subscription>
</template>


<script>
import UserForm from '../../components/User/UserForm.vue'
import UserSubscription from '../../components/User/UserSubscription.vue'

import { mapActions } from 'vuex'

export default {
    components: {
        UserForm,
        UserSubscription,
    },

    created() {
        this.loadUser()
        this.removeAuthErrors()
    },

    computed: {
        authErrors() {
            const errors = this.$store.getters['auth/authErrors']
            let errorMessage = ''

            for (const error in errors) {
                errorMessage += errors[error] + '\n'
            }

            return errorMessage
        },
    },

    methods: {
        ...mapActions({
            loadUser: 'auth/fetchUser',
            removeAuthErrors: 'auth/removeAuthErrors',
        }),

        async updateUser(user) {
            axios.get("/sanctum/csrf-cookie").then(response => {
                this.$store.dispatch('auth/update', user)
            })
        },
    },
}
</script>
