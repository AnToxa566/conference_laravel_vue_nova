<template>
    <v-dialog
        :model-value="modelValue"
        fullscreen
        :scrim="false"
    >
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
                :conferenceId="this.conference.id"
                @submit="createLecture"
            >
                <template v-slot:extraButtons>
                    <v-btn
                        variant="tonal" color="white" class="mx-2"
                        @click="this.$emit('update:modelValue', false)"
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

    emits: ['update:modelValue'],

    data: () => ({
        dialog: false,
    }),

    props: {
        modelValue: [Boolean],

        conference: {
            type: Object,
            required: true,
        },
    },

    created() {
        this.$store.commit('lecture/SET_ERROR', '')
    },

    computed: {
        getFreeStartTime() {
            return this.$store.getters['lecture/getFreeStartTime'](this.conference)
        },

        error() {
            return this.$store.getters['lecture/error']
        },
    },

    methods: {
        async createLecture(lecture) {
            this.$store.dispatch('lecture/storeLecture', lecture)
            this.$emit('update:modelValue', false)
        },
    },
}
</script>
