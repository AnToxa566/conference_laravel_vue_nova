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

import { mapGetters } from 'vuex'

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
        this.$store.commit('comment/SET_COMMENTS_OF_LECTURE', [])

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
        ...mapGetters({
            lectures: 'lecture/lectures',
            comments: 'comment/commentsOfLecture',
        }),

        commentsCount() {
            return this.lectures.find(lec => lec.id == this.lecture_id).comments_count
        },
    },

    methods: {
        async loadMoreComments() {
            try {
                const result = await axios.get(
                    `/api/comments/${this.lecture_id}/limit/${this.limit}/page/${this.page}`,
                    JSON.parse(localStorage.getItem('config'))
                )

                if (result.data.length) {
                    if (this.page == 1) {
                        this.$store.commit('comment/SET_COMMENTS_OF_LECTURE', result.data)
                    }
                    else {
                        this.$store.commit('comment/PUSH_COMMENTS', result.data)
                    }

                    this.page++
                }
            }
            catch(err) {
                console.log(err.response)
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
