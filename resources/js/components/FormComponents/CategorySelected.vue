<template>
    <v-dialog
        v-model="showDialog"
        max-height="100vh"
        persistent
    >
         <template
            v-slot:activator
        >
            <div @click="showDialog = true">
                <v-select
                    v-model="select"
                    @click:clear="this.$emit('clear')"

                    item-title="text"
                    item-value="id"

                    variant="solo"
                    label="Choose category"

                    hide-no-data
                    clearable
                >
                </v-select>
            </div>
        </template>

        <v-container>
            <v-card class="pa-4">
                <v-card-title class="mb-6">
                    <span class="text-h5">Choose a category</span>
                </v-card-title>

                <v-card-text>
                    <category-tree
                        :storeRoots="this.roots"
                        :storeNodes="this.nodes"

                        @categorySelected="onSelect"
                    >
                    </category-tree>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        variant="text" color="white" class="mx-2"
                        @click="onCloseClick"
                    >
                        Close
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-container>
    </v-dialog>
</template>


<script>
import CategoryTree from '../../components/Category/CategoryTree.vue'

export default {
    name: 'category-selected',

    components: {
        CategoryTree,
    },

    props: {
        roots: {
            type: Array,
            required: true,
        },

        nodes: {
            type: Object,
            required: true,
        },

        defaultSelect: {
            type: Object,
            required: false,
        },
    },

    emits: [
        'select',
        'clear',
    ],

    data: () => ({
        showDialog: false,
        select: {
            text: '',
            id: '',
        },
    }),

    created() {
        if (this.defaultSelect) {
            this.select = this.defaultSelect
        }
    },

    methods: {
        onSelect(event) {
            this.select = {}

            this.select.text = event.text
            this.select.id = event.id

            this.showDialog = false

            this.$emit('select', this.select)
        },

        onCloseClick() {
            this.showDialog = false
        },
    },
}
</script>
