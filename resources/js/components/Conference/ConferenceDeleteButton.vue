<template>
    <v-btn variant="text" color="red" class="mx-1" @click="this.confirmationDialog = true"> Delete </v-btn>

    <action-confirmation
        v-model="confirmationDialog"

        title="Delete conference?"
        text="Are you sure you want to delete this conference?"

        @confirm="this.delete"
    >
    </action-confirmation>
</template>


<script>
export default {
    name: 'conference-delete-button',

    props: {
        conferenceId: {
            type: Number,
            required: true,
        },
    },

    data: () => ({
        confirmationDialog: false,
    }),

    methods: {
        delete(event) {
            if (event) {
                this.$store.dispatch('conference/deleteConference', this.conferenceId)
            }
        },
    },
}
</script>
