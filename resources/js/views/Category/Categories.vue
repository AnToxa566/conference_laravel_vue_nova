<template>
    <my-header>
        <template v-slot:header>Categories</template>
    </my-header>

    <category-tree
        :storeRoots="this.roots"
        :storeNodes="this.nodes"

        @addClick="onAddClick"
        @removeClick="onRemoveClick"
    >
    </category-tree>

    <category-add-dialog
        v-model:showDialog="showDialog"
        @saveData="addCategory"
    >
    </category-add-dialog>
</template>


<script>
import CategoryTree from '../../components/Category/CategoryTree.vue'
import CategoryAddDialog from '../../components/Category/CategoryAddDialog.vue'

export default {
    components: {
        CategoryTree,
        CategoryAddDialog,
    },

    data: () => ({
        showDialog: false,
        parentNode: null,
    }),

    computed: {
        roots() {
            return this.$store.getters['category/roots']
        },

        nodes() {
            return this.$store.getters['category/nodes']
        },
    },

    methods: {
        onAddClick(event) {
            this.showDialog = true
            this.parentNode = event
        },

        onRemoveClick(event) {
            this.$store.dispatch('category/deleteCategory', event.id)
        },

        addCategory(event) {
            this.$store.dispatch('category/storeCategory', {
                parent_id: parseInt(this.parentNode.id),
                title: event,
            })
        }
    },
}
</script>
