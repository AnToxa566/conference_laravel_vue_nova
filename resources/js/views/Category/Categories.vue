<template>
    <my-header>
        <template v-slot:header>Categories</template>
    </my-header>

    <category-tree
        :roots="this.roots"
        :nodes="this.nodes"
    >
    </category-tree>

    <v-btn variant="tonal" color="white" @click="$router.go(-1)"> Create category </v-btn>
</template>


<script>
import CategoryTree from '../../components/Category/CategoryTree.vue'

export default {
    components: {
        CategoryTree,
    },

    computed: {
        roots() {
            return this.$store.getters['category/getRoots']
        },

        nodes() {
            const categories = this.$store.getters['category/categories']
            const subcategories = this.$store.getters['category/subcategories']

            const nodes = {}

            categories.forEach(category => {
                const subcategoriesOfCategory = subcategories.find(subcategory => subcategory.category_id === category.id)
                let categoryChildren = []
                let categoryChildrenId = []

                if (subcategoriesOfCategory) {
                    categoryChildren = subcategoriesOfCategory['children']
                }


                if (categoryChildren.length !== 0) {
                    categoryChildrenId = categoryChildren.map(child => String(child.id))
                }

                nodes[category.id] = {
                    text: category.title,
                    children: categoryChildrenId,
                }
            });

            return nodes
        },
    },
}
</script>
