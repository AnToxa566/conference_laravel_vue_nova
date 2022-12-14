import axios from 'axios'
import store from '../store'

export default {
    namespaced: true,

    state: {
        categories: [],
        nodes: {},
    },

    getters: {
        categories(state) {
            return state.categories
        },

        roots(state) {
            return state.categories.filter(category => !category.parent_id).map(category => category.id)
        },

        nodes(state) {
            return state.nodes
        },
    },

    mutations: {
        SET_CATEGORIES (state, categories) {
            state.categories = categories
        },

        SET_NODES (state, categories) {
            const nodes = {}

            categories.forEach(category => {
                nodes[category.id] = {
                    text: category.title,
                    parent: category.parent_id ? String(category.parent_id) : null,
                    children: category.children.map(child => String(child.id)),
                    state: {
                        opened: false,
                        disabled: false,
                        checked: false,
                    },
                }
            });

            state.nodes = nodes
        },

        PUSH_CATEGORY (state, category) {
            state.categories.push(category)

            if (category.parent_id) {
                const index = state.categories.map(cat => cat.id).indexOf(category.parent_id);

                if (index !== -1) {
                    state.categories[index].children.push(category)
                }
            }
        },

        PUSH_NODE (state, category) {
            state.nodes[category.id] = {
                text: category.title,
                parent: category.parent_id ? String(category.parent_id) : null,
                children: category.children.map(child => String(child.id)),
                state: {
                    opened: false,
                    disabled: false,
                    checked: false,
                },
            }
        },

        REMOVE_CATEGORY (state, categories) {
            categories.forEach(category => {
                const index = state.categories.map(cat => cat.id).indexOf(category.id)
                state.categories.splice(index, 1)

                delete state.nodes[category.id]
            })
        },
    },

    actions: {
        fetchAllCategories({ commit }) {
            axios.get('/api/category')
                .then(res => {
                    if (res.data.status === 'ok') {
                        commit('SET_CATEGORIES', res.data.categories)
                        commit('SET_NODES', res.data.categories)
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
                        commit('PUSH_NODE', (res.data.category))
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
                        commit('REMOVE_CATEGORY', res.data.items)
                    }
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
