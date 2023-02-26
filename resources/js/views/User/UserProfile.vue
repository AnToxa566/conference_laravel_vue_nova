<template>
    <custom-header>
        <template v-slot:header>Profile</template>
    </custom-header>

    <custom-error-alert
        :errorMessage='this.errorMessage'
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

import { mapGetters, mapActions } from 'vuex'

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
        ...mapGetters({
            authErrors: 'auth/authErrors',
        }),

        errorMessage() {
            let errorMessage = ''

            for (const error in this.authErrors) {
                errorMessage += this.authErrors[error] + '\n'
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
