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
        <v-btn variant="tonal" color="white" class="mx-1" @click="this.submitOn()"> Save </v-btn>
        <v-btn variant="text" color="white" class="mx-1" @click="this.cancelOn()"> Cancel </v-btn>
    </div>
</template>


<script>
import { mapGetters } from 'vuex'

export default {
    emits: [
        'submit',
        'cancel',
    ],

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
        contentToEdit: {
            type: String,
            required: false,
        },
    },

    created() {
        if (this.contentToEdit) {
            this.content = this.contentToEdit
        }
    },

    mounted() {
        this.quill = this.$refs.editor

        this.comment.user_id = this.user.id
        this.comment.lecture_id = this.lecture_id
    },

    computed: {
        ...mapGetters({
            user: 'auth/user',
        }),
    },

    methods: {
        cancelOn() {
            this.quill.setContents([])
            this.$emit('cancel')
        },

        async submitOn() {
            if (this.quill.getText().trim() != '') {
                this.comment.description = this.content
                this.$emit('submit', this.comment)

                this.quill.setContents([])
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
