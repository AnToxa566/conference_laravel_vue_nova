import axios from 'axios'
import store from '../store'

export default {
    namespaced: true,

    state: {
        categories: [],
        subcategories: [],
    },

    getters: {
        categories(state) {
            return state.categories
        },
        subcategories(state) {
            return state.subcategories
        },

        getRoots(state) {
            return state.categories.filter(category => !category.parent_id).map(category => category.id)
        },
        getNodes(state) {
            const nodes = {}

            state.categories.forEach(category => {
                const subcategoriesOfCategory = state.subcategories.find(subcategory => subcategory.category_id === category.id)

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
                    parent: category.parent_id ? String(category.parent_id) : null,
                    children: categoryChildrenId,
                    state: {
                        opened: false,
                        disabled: false,
                        checked: false,
                    },
                }
            });

            return nodes
        },
    },

    mutations: {
        SET_CATEGORIES (state, value) {
            state.categories = value
        },
        SET_SUBCATEGORIES (state, value) {
            state.subcategories = value
        },

        PUSH_CATEGORY (state, value) {
            state.categories.push(value)

            if (value.parent_id) {
                const index = state.subcategories.map(subcategory => subcategory.category_id).indexOf(value.parent_id);

                if (index !== -1) {
                    state.subcategories[index].children.push(value)
                }
                else {
                    state.subcategories.push({
                        category_id: value.parent_id,
                        children: [ value ],
                    })
                }
            }
        },

        REMOVE_CATEGORY (state, categoryId) {
            const mapCategoriesId = state.categories.map(c => c.id)
            const mapSubcategoriesId = state.subcategories.map(c => c.category_id)

            console.log('mapCategoriesId')
            console.log(mapCategoriesId)
            console.log('mapSubcategoriesId')
            console.log(mapSubcategoriesId)
            console.log('categoryId')
            console.log(categoryId)

            let categoryIndex = mapSubcategoriesId.indexOf(parseInt(categoryId, 10))

            console.log('categoryIndex')
            console.log(categoryIndex)

            if (categoryIndex !== -1) {
                let childrenId = state.subcategories[categoryIndex].children.map(c => c.id)

                console.log('childrenId')
                console.log(childrenId)

                for (let i = 0; i < childrenId.length; i++) {
                    let subcategory = state.subcategories.find(c => c.category_id === childrenId[i])

                    if (subcategory) {
                        subcategory.children.forEach(c => childrenId.push(c.id))
                    }
                }

                console.log('childrenId')
                console.log(childrenId)

                for (let i = 0; i < childrenId.length; i++) {
                    let childrenIndex = mapCategoriesId.indexOf(childrenId[i])

                    console.log('childrenIndex')
                    console.log(childrenIndex)

                    state.categories.splice(childrenIndex, 1);

                    childrenIndex = mapSubcategoriesId.indexOf(childrenId[i])

                    console.log('childrenIndex')
                    console.log(childrenIndex)

                    state.subcategories.splice(childrenIndex, 1);
                }
            }

            categoryIndex = mapCategoriesId.indexOf(parseInt(categoryId, 10))
            state.categories.splice(categoryIndex, 1);

            console.log('After')
            console.log('state.categories')
            console.log(state.categories)
            console.log('state.subcategories')
            console.log(state.subcategories)
        },
    },

    actions: {
        fetchAllCategories({ commit }) {
            axios.get('/api/category')
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_CATEGORIES', res.data.categories)
                        commit('SET_SUBCATEGORIES', res.data.subcategories)
                    }
                })
                .catch(err => {
                    console.log(err)
                })
        },

        storeCategory({ commit }, request) {
            axios.post('/api/category/add', request)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('PUSH_CATEGORY', (res.data.category))
                    }
                })
                .catch(err => {
                    console.log(err)
                })
        },

        deleteCategory({ commit }, categoryId) {
            axios.get(`/api/category/${categoryId}/delete`)
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('REMOVE_CATEGORY', categoryId)
                    }
                })
                .catch(err => {
                    console.log(err)
                })
        },
    }
}
