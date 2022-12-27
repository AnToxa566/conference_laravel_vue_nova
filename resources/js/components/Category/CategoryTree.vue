<template>
    <Tree
        :nodes="this.storeNodes"
        :config="this.config"
        @nodeFocus="nodeFocus"
    >
        <template #after-input="props">
            <v-icon
                v-if="this.addition"
                class="mx-3"
                size="small"
                color="white"
                @click="this.onAddClick(props)"
            >
                mdi-tag-plus-outline
            </v-icon>

            <v-icon
                v-if="this.deletion"
                size="small"
                color="red-darken-2"
                @click="this.openConfirmationDialog(props)"
            >
                mdi-tag-minus-outline
            </v-icon>

            <action-confirmation
                v-model="confirmationDialog"

                title="Delete category?"
                text="Are you sure you want to delete this category?"

                @confirm="this.onRemoveClick"
            >
            </action-confirmation>
        </template>
    </Tree>
</template>


<script>
export default {
    name: 'category-tree',

    emits: [
        'addClick',
        'removeClick',
        'categorySelected',
    ],

    props: {
        storeRoots: {
            type: Array,
            required: true,
            default: [],
        },

        storeNodes: {
            type: Object,
            required: true,
            default: {},
        },

        addition: {
            type: Boolean,
            required: false,
            default: false,
        },

        deletion: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    data: () => ({
        config: {
            roots: [],

            openedIcon: {
                type: "shape",
                stroke: "white",
                strokeWidth: 3,
                viewBox: "0 0 24 24",
                draw: "M 2 12 L 22 12",
            },

            closedIcon: {
                type: "shape",
                stroke: "white",
                strokeWidth: 3,
                viewBox: "0 0 24 24",
                draw: `M 12 2 L 12 22 M 2 12 L 22 12`,
            },
        },

        confirmationDialog: false,
        nodeToDelete: null,
    }),

    created() {
        this.config.roots = this.storeRoots.map(root => String(root))
    },

    methods: {
        nodeFocus(event) {
            this.$emit('categorySelected', event)
        },

        openConfirmationDialog(props) {
            this.nodeToDelete = props.node
            this.confirmationDialog = true
        },

        onAddClick(props) {
            this.$emit('addClick', props.node)
        },

        onRemoveClick(event) {
            if (event) {
               this.$emit('removeClick', this.nodeToDelete)
            }
            else {
                this.nodeToDelete = null
            }

        },
    },
}
</script>


<style>
    .node-text {
        color: white;
    }

    .node-wrapper:hover {
        background-color: #181616;
    }
</style>
