<template>
    <div
        class="d-flex mb-3"
    >
        <v-avatar color="grey-darken-4" class="me-4">
            <v-icon icon="mdi-account"></v-icon>
        </v-avatar>

        <div
            class="w-100"
        >
            <div
                class="mb-1"
            >
                <span class="font-weight-bold me-2">{{ this.comment.first_name + ' ' + this.comment.last_name }}</span>
                <span class="text-disabled text-caption me-2">{{ this.formattedCreatedAt }}</span>

                <v-btn
                    variant="text"
                    size="small"
                    color="white"
                    class="font-weight-bold"
                    @click="this.showEditForm()"
                >
                    <v-icon icon="mdi-pencil" size="small"></v-icon>
                </v-btn>
            </div>

            <div v-html="this.comment.description"></div>
        </div>
    </div>
</template>


<script>
import moment from 'moment'

export default {
    props: {
        comment: {
            type: Object,
            required: true,
        },
    },

    computed: {
        formattedCreatedAt() {
            const format = 'YYYY-MM-DD'

            return moment(this.comment.created_at).format(format)
        },
    },

    methods: {
        showEditForm() {

        },
    },
}
</script>


<style scoped>

</style>
