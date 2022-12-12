<template>
    <my-header>
        <template v-slot:header>Profile</template>
    </my-header>

    <custom-error-alert
        :errorMessage='this.authErrors'
        class="mb-6"
    >
    </custom-error-alert>

    <user-form
        @submit="updateUser"
    ></user-form>
</template>


<script>
import UserForm from '../../components/User/UserForm.vue';

export default {
    components: {
        UserForm,
    },

    created() {
        this.$store.dispatch('auth/removeAuthErrors')
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
        async updateUser(user) {
            console.log(user)

            axios.get("/sanctum/csrf-cookie").then(response => {
                this.$store.dispatch('auth/update', user)
            })
        },
    },
}
</script>
