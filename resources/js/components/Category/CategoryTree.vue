<template>
    <Tree :nodes="this.storeNodes" :config="this.config">
        <template #after-input="props">
            <v-icon
                class="mx-3"
                size="small"
                color="white"
                @click="this.onAddClick(props)"
            >
                {{ this.addTagIcon }}
            </v-icon>

            <v-icon
                size="small"
                color="red-darken-2"
                @click="this.onRemoveClick(props)"
            >
                {{ this.removeTagIcon }}
            </v-icon>
        </template>
    </Tree>
</template>


<script>
export default {
    name: 'category-tree',

    emits: [
        'addClick',
        'removeClick',
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

        addTagIcon: 'mdi-tag-plus-outline',
        removeTagIcon: 'mdi-tag-minus-outline',
    }),

    created() {
        this.config.roots = this.storeRoots.map(root => String(root))
    },

    methods: {
        onAddClick(props) {
            this.$emit('addClick', props.node)
        },

        onRemoveClick(props) {
            this.$emit('removeClick', props.node)
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
