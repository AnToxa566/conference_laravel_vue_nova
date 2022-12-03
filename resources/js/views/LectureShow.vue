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

    <div
        v-if="isUserOwnThisLecture"
        class="d-flex"
    >
        <v-btn variant="tonal" color="white" class="mx-1" @click="$router.push(`/conferences/${this.conference_id}/lectures/${this.lecture_id}/edit`)"> Edit </v-btn>
        <v-btn variant="tonal" color="white" class="mx-1" @click="this.cancelParticipation()"> Сancel participation </v-btn>
    </div>
</template>


<script>
import moment from 'moment'

export default {
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
            this.$store.dispatch('user_conferences/cancelParticipation', {
                'conference_id': this.conference_id,
                'user_id': this.userId,
            })

            this.$store.dispatch('lecture/deleteLecture', {
                'conference_id': this.conference_id,
                'user_id': this.userId,
            })

            this.$router.push({ name: 'conferences' })
        },
    }
}
</script>


<style scoped>

</style>
