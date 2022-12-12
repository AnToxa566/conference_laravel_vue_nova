<template>
    <Tree :nodes="dataNodes" :config="config"></Tree>
</template>


<script>
export default {
    name: 'category-tree',

    props: {
        roots: {
            type: Array,
            required: true,
            default: [],
        },

        nodes: {
            type: Object,
            required: true,
            default: {},
        },
    },

    data: () => ({
        config: {
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

        dataNodes: null,
    }),

    created() {
        this.config.roots = this.roots.map(root => String(root))
        this.dataNodes = this.nodes
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
