<template>
    <my-header>
        <template v-slot:header>Lecture details</template>
    </my-header>

    <my-info-card>
        <template v-slot:header> Topic </template>
        <template v-slot:body> {{ this.lecture.title }} </template>
    </my-info-card>

    <my-info-card>
        <template v-slot:header> Date </template>
        <template v-slot:body> {{ this.formattedDate }} </template>
    </my-info-card>

    <my-info-card>
        <template v-slot:header> Time </template>
        <template v-slot:body>
            from <span class="font-weight-bold">{{ this.startFormattedTime }}</span>
            to <span class="font-weight-bold">{{ this.endFormattedTime }}</span>
        </template>
    </my-info-card>

    <my-info-card>
        <template v-slot:header> Description </template>
        <template v-slot:body> {{ this.lecture.description }} </template>
    </my-info-card>

    <my-info-card>
        <template v-slot:header> Presentation </template>
        <template v-slot:body> TODO </template>
    </my-info-card>

    <my-info-card
        v-if="this.category"
    >
        <template v-slot:header> Category </template>
        <template v-slot:body> {{ this.category.title }} </template>
    </my-info-card>

    <div
        v-if="isUserOwnThisLecture"
        class="d-flex"
    >
        <v-btn variant="tonal" color="white" class="mx-1" @click="$router.push(`/conferences/${this.conference_id}/lectures/${this.lecture_id}/edit`)"> Edit </v-btn>
        <v-btn variant="text" color="white" class="mx-1" @click="this.cancelParticipation()"> Сancel participation </v-btn>
    </div>

    <div class="py-3 mt-6 text-h6 font-weight-bold">
        Comments
    </div>

    <comment-form
        :lecture_id="this.lecture_id"
        @submit="storeComment"
    ></comment-form>
    <comment-list
        :lecture_id="this.lecture_id"
    ></comment-list>
</template>


<script>
import CommentForm from '../../components/Comment/CommentForm.vue'
import CommentList from '../../components/Comment/CommentList.vue'
import moment from 'moment'

export default {
    components: {
        CommentForm,
        CommentList,
    },

    data() {
        return {
            conference_id: null,
            lecture_id: null,
        };
    },

    created() {
        this.conference_id = parseInt(this.$route.params.conference_id, 10);
        this.lecture_id = parseInt(this.$route.params.lecture_id, 10);

        this.$store.dispatch('lecture/fetchLectureById', this.lecture_id)
    },

    computed: {
        lecture() {
            return this.$store.getters['lecture/lecture']
        },
        category() {
            return this.$store.getters['category/categoryById'](this.lecture.category_id)
        },

        userId() {
            return this.$store.getters['auth/user'].id
        },

        isUserOwnThisLecture() {
            return this.$store.getters['lecture/isUserOwnThisLecture']
        },

        formattedDate() {
            const format = 'YYYY-MM-DD'
            const date = new Date(this.lecture.date_time_start)

            return moment(date).format(format)
        },
        startFormattedTime() {
            const format = 'HH:mm'
            const date = new Date(this.lecture.date_time_start)

            return moment(date).format(format)
        },
        endFormattedTime() {
            const format = 'HH:mm'
            const date = new Date(this.lecture.date_time_end)

            return moment(date).format(format)
        },
    },

    methods: {
        cancelParticipation() {
            this.$store.dispatch('lecture/cancelParticipation', this.conference_id)
        },

        async storeComment(comment) {
            this.$store.dispatch('comment/storeComment', comment)
            this.$store.dispatch('lecture/incrementCommentsCount', this.lecture_id)
        }
    }
}
</script>


<style scoped>

</style>
