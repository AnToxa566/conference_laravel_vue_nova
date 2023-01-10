import axios from 'axios'
import store from '../store'
import router from '../router'

export default {
    namespaced: true,

    state: {
        categories: [],

        roots: [],
        nodes: {},

        lectureRoots: [],
        lectureNodes: {},
    },

    getters: {
        categories(state) {
            return state.categories
        },

        categoryById: (state) => (id) => {
            return state.categories.find(category => category.id === parseInt(id, 10));
        },

        roots(state) {
            return state.roots
        },

        nodes(state) {
            return state.nodes
        },

        lectureRoots(state) {
            return state.lectureRoots
        },

        lectureNodes(state) {
            return state.lectureNodes
        },

        getPathByLeafId: (state) => (leafId) => {
            let leaf = state.categories.find(category => category.id === parseInt(leafId, 10))
            let path = []

            if (leaf) {
                path.unshift(leaf)

                while (leaf.parent_id) {
                    leaf = state.categories.find(category => category.id === parseInt(leaf.parent_id, 10))
                    if (leaf) path.unshift(leaf)
                }
            }

            return path
        }
    },

    mutations: {
        SET_CATEGORIES (state, categories) {
            state.categories = categories
        },

        SET_ROOTS (state, categories) {
            state.roots = categories.filter(category => !category.parent_id).map(category => category.id)
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
                    conferences_count: category.conferences_count,
                    lectures_count: category.lectures_count,
                }
            });

            state.nodes = nodes
        },

        SET_LECTURE_ROOTS (state, parentId) {
            state.lectureRoots = []
            state.lectureRoots.push(parentId)
        },

        SET_LECTURE_NODES (state, parentId) {
            const categories = []
            const nodes = {}

            let category = state.categories.find(cat => cat.id === parentId)
            categories.push(category)

            for (let i = 0; i < categories.length; i++) {
                if (categories[i].children.length) {
                    categories[i].children.forEach(child => {
                        category = state.categories.find(cat => cat.id === child.id)
                        categories.push(category)
                    })
                }
            }

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

            state.lectureNodes = nodes
        },

        PUSH_CATEGORY (state, category) {
            state.categories.push(category)

            if (category.parent_id) {
                const index = state.categories.map(cat => cat.id).indexOf(category.parent_id)

                if (index !== -1) {
                    state.categories[index].children.push(category)
                }
            }
        },

        PUSH_ROOT (state, category) {
            state.roots.push(category.id)
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
                const nodeIndex = state.categories.map(cat => cat.id).indexOf(category.id)
                state.categories.splice(nodeIndex, 1)

                if (!category.parent_id) {
                    const rootIndex = state.roots.indexOf(category.id)
                    state.roots.splice(rootIndex, 1)
                }

                delete state.nodes[category.id]
            })
        },
    },

    actions: {
        fetchAllCategories({ commit }) {
            axios.get('/api/categories')
                .then(res => {
                    commit('SET_CATEGORIES', res.data)

                    commit('SET_NODES', res.data)
                    commit('SET_ROOTS', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        fetchBranche({ commit }, parentId) {
            commit('SET_LECTURE_ROOTS', parentId)
            commit('SET_LECTURE_NODES', parentId)
        },

        storeCategory({ commit }, request) {
            axios.post('/api/categories/add', request, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('PUSH_CATEGORY', (res.data))
                    commit('PUSH_NODE', (res.data))

                    if (!res.data.parent_id) {
                        commit('PUSH_ROOT', (res.data))
                    }

                    router.go(0)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        deleteCategory({ commit }, categoryId) {
            axios.get(`/api/categories/${categoryId}/delete`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('REMOVE_CATEGORY', res.data)

                    store.dispatch('conference/updateConferenceCategories', res.data)
                    store.dispatch('lecture/updateLectureCategories', res.data)

                    router.go(0)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
