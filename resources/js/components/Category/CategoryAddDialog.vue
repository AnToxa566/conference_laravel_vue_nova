<template>
    <v-dialog
        v-model="this.showDialog"
        max-height="100vh"
        persistent
    >
        <v-container>
            <v-card class="pa-4">
                <v-card-title class="mb-6">
                    <span class="text-h5">Add Category</span>
                </v-card-title>

                <v-card-text>
                    <custom-input
                        v-model="categoryName"
                        label="Category name"
                        :rules="[v => !!v || 'Field is required!']"
                    >
                    </custom-input>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        variant="text" color="white" class="mx-2"
                        @click="onCloseClick"
                    >
                        Close
                    </v-btn>

                    <v-btn
                        variant="tonal" color="white" class="mx-2"
                        @click="onSaveClick"
                    >
                        Save
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-container>
    </v-dialog>
</template>


<script>
export default {
    emits: [
        'update:showDialog',
        'saveData',
    ],

    data: () => ({
        categoryName: '',
    }),

    props: {
        showDialog: {
            type: Boolean,
            required: true,
            default: false,
        },
    },

    methods: {
        onSaveClick() {
            if (this.categoryName) {
                this.$emit('update:showDialog', false)
                this.$emit('saveData', this.categoryName)
            }
        },

        onCloseClick() {
            this.$emit('update:showDialog', false)
        },
    },
}
</script>
