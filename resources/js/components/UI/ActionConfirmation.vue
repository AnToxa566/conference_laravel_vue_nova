<template>
    <v-dialog
        :model-value="modelValue"
        max-width="600"
        persistent
    >
        <v-card class="pa-2">
            <v-card-title class="text-h5">
                {{ this.title }}
            </v-card-title>

            <v-card-text>
                {{ this.text }}
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn
                    color="white"
                    variant="tonal"
                    @click="confirm(false)"
                >
                    Cancel
                </v-btn>

                <v-btn
                    color="error"
                    variant="tonal"
                    @click="confirm(true)"
                >
                    Confirm
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>


<script>
export default {
    name: 'action-confirmation',

    emits: [
        'update:modelValue',
        'confirm',
    ],

    props: {
        modelValue: [Boolean],
        title: [String],
        text: [String],
    },

    methods: {
        confirm(isConfirm) {
            this.$emit('confirm', isConfirm)
            this.$emit('update:modelValue', false)
        },
    },
}
</script>
