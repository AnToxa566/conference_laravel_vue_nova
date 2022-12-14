<template>
    <comment-item
        v-for="comment in this.comments"
        :key="comment.id"
        :comment="comment"
    >
    </comment-item>

    <div ref="observer" class="observer"></div>

    <div
        v-if="!comments.length"
        class="text-medium-emphasis text-center"
    >
        Comments not added yet
    </div>
</template>


<script>
import CommentItem from './CommentItem.vue'
import axios from "axios";

export default {
    components: {
        CommentItem,
    },

    data: () => ({
        page: 1,
        limit: 5,
    }),

    props: {
        lecture_id: {
            type: Number,
            required: true,
        },
    },

    mounted() {
        this.loadMoreComments()

        const options = {
            rootMargin: '0px',
            threshold: 1.0
        }

        const callback = (entries, observer) => {
            if (entries[0].isIntersecting && this.comments.length < this.commentsCount) {
                this.loadMoreComments()
            }
        };

        const observer = new IntersectionObserver(callback, options);
        observer.observe(this.$refs.observer)
    },

    computed: {
        commentsCount() {
            return this.$store.getters['lecture/commentsCounts'].find(counts => parseInt(counts.lecture_id, 10) === this.lecture_id).comments_count
        },

        comments() {
            return this.$store.getters['comment/commentsOfLecture']
        },
    },

    methods: {
        async loadMoreComments() {
            try {
                const result = await axios.get(`/api/comments/${this.lecture_id}/limit/${this.limit}/page/${this.page}`)

                this.$store.dispatch('comment/fetchMoreCommentsOfLecture', {
                    'lecture_id': this.lecture_id,
                    'limit': 10,
                    'page': this.page,
                })

                if (result.data.comments.length) {
                    this.page++
                }
            } catch(err) {
                console.log(err)
            }
        }
    },
}
</script>


<style scoped>
    .observer {
        height: 30px;
        background-color: none  ;
    }
</style>
