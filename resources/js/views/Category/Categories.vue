<template>
    <custom-header>
        <template v-slot:header>Categories</template>
    </custom-header>

    <category-tree
        :storeRoots="this.roots"
        :storeNodes="this.nodes"

        :addition="true"
        :deletion="true"

        @addClick="onAddClick"
        @removeClick="onRemoveClick"
    >
    </category-tree>

    <v-btn
        size="small"
        variant="tonal"
        color="white"
        class="my-2"

        @click="onAddRootClick"
    >
        Add root category
    </v-btn>

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

        onAddRootClick() {
            this.showDialog = true
            this.parentNode = null
        },

        onRemoveClick(event) {
            this.$store.dispatch('category/deleteCategory', event.id)
        },

        addCategory(event) {
            this.$store.dispatch('category/storeCategory', {
                parent_id: this.parentNode ? parseInt(this.parentNode.id) : null,
                title: event,
            })
        }
    },
}
</script>
