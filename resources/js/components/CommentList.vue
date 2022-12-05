<template>
    <comment-item
        v-for="comment in commentsOfLecture"
        :key="comment.id"
        :comment="comment"
    >
    </comment-item>

    <div
        v-if="!commentsOfLecture.length"
        class="text-medium-emphasis text-center"
    >
        Comments not added yet
    </div>
</template>


<script>
import CommentItem from './CommentItem.vue'

export default {
    components: {
        CommentItem,
    },

    props: {
        lecture_id: {
            type: Number,
            required: true,
        },
    },

    created() {
        this.$store.dispatch('comment/fetchCommentsOfLecture', this.lecture_id)
    },

    computed: {
        commentsOfLecture() {
            return this.$store.getters['comment/commentsOfLecture']
        },
    },
}
</script>


<style scoped>

</style>
