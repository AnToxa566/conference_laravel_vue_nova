<template>
    <div class="d-flex">
        <v-avatar color="grey-darken-4" class="me-4">
            <v-icon icon="mdi-account"></v-icon>
        </v-avatar>

        <div class="d-flex align-center ">
            <span class="font-weight-bold me-2">{{ this.comment.first_name + ' ' + this.comment.last_name }}</span>
            <span class="text-disabled text-caption me-2">{{ this.formattedCreatedAt }}</span>

            <v-btn
                v-if="this.canBeUpdated"
                variant="text"
                size="small"
                color="white"
                class="font-weight-bold"
                @click="this.showEditForm()"
            >
                <v-icon icon="mdi-pencil" size="small"></v-icon>
            </v-btn>
        </div>
    </div>

    <div
        v-if="!this.editMode"
        v-html="this.comment.description"
        class="ms-14"
    ></div>
    <div
        v-else
        class="mt-4"
    >
        <comment-form
            :lecture_id="this.comment.lecture_id"
            :contentToEdit="this.comment.description"
            @submit="updateComment"
            @cancel="(this.editMode = false)"
        ></comment-form>
    </div>
</template>


<script>
import CommentForm from '../components/CommentForm.vue'
import moment from 'moment'

export default {
    components: {
        CommentForm,
    },

    data: () => ({
        editMode: false,
    }),

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

        canBeUpdated() {
            return this.$store.getters['comment/canBeUpdated'](this.comment.id)
        },
    },

    methods: {
        showEditForm() {
            this.editMode = true
        },

        async updateComment(comment) {
            this.comment.description = comment.description
            this.$store.dispatch('comment/updateComment', this.comment)

            this.editMode = false
        },
    },
}
</script>


<style scoped>

</style>
