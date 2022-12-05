<template>
    <QuillEditor
        v-model:content="content"
        contentType="html"
        ref="editor"
        theme="snow"
        toolbar="essential"
        placeholder="Add a comment..."
    />

    <div
        class="d-flex mt-4 mb-6"
    >
        <v-btn variant="tonal" color="white" class="mx-1" @click="this.saveComment()"> Save </v-btn>
        <v-btn variant="text" color="white" class="mx-1" @click="this.clearContent()"> Cancel </v-btn>
    </div>
</template>


<script>
export default {
    data: () => ({
        content: '',
        quill: null,

        comment: {
            user_id: null,
            lecture_id: null,

            description: '',
        },
    }),

    props: {
        lecture_id: {
            type: Number,
            required: true,
        },
    },

    mounted() {
        this.quill = this.$refs.editor

        this.comment.user_id = this.$store.getters['auth/user'].id
        this.comment.lecture_id = this.lecture_id
    },

    methods: {
        clearContent() {
            this.quill.setContents([])
        },

        async saveComment() {
            if (this.quill.getText().trim() !== '') {
                this.comment.description = this.content

                this.$store.dispatch('comment/storeComment', this.comment)
                this.clearContent()
            }
        }
    },
}
</script>

<style>
    .ql-editor {
        padding: 30px 20px;
    }

    .ql-editor.ql-blank::before{
        color: grey;
    }
</style>
