<template>
    <v-dialog
        v-model="dialog"
        fullscreen
        :scrim="false"
    >
        <template
            v-slot:activator="{ props }"
        >
            <v-btn
                v-if="this.getFreeStartTime"
                variant="tonal" color="white" class="mx-1"
                v-bind="props"
            >
                Join
            </v-btn>

            <div
                v-else
                class="d-flex align-center mx-1"
            >
                <p class="mb-0">Registration is impossible</p>
            </div>
        </template>

        <v-card class="pa-8">
            <v-card-title class="mb-6">
                <span class="text-h4 font-weight-bold">Add Lecture</span>
            </v-card-title>

            <custom-error-alert
                :errorMessage='this.error'
                class="mb-6"
            >
            </custom-error-alert>

            <lecture-form
                :conference="this.conferenceById"
                @submit="createLecture"
            >
                <template v-slot:extraButtons>
                    <v-btn
                        variant="tonal" color="white" class="mx-2"
                        @click="dialog = false"
                    >
                        Close
                    </v-btn>
                </template>
            </lecture-form>
        </v-card>
    </v-dialog>
</template>


<script>
import LectureForm from './LectureForm.vue'

export default {
    components: {
        LectureForm,
    },

    data: () => ({
        dialog: false,
    }),

    props: {
        conferenceId: {
            type: Number,
            required: true,
        },
    },

    created() {
        this.$store.commit('lecture/SET_ERROR', '')
    },

    computed: {
        conferenceById() {
            return this.$store.getters['conference/conferenceById'](this.conferenceId)
        },

        getFreeStartTime() {
            return this.$store.getters['lecture/getFreeStartTime'](this.conferenceById)
        },

        error() {
            return this.$store.getters['lecture/error']
        },
    },

    methods: {
        async createLecture(lecture) {
            this.$store.dispatch('lecture/storeLecture', lecture)
            this.dialog = false
        },
    },
}
</script>
