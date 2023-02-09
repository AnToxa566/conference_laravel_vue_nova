import axios from 'axios'

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
        categories: state => state.categories,

        categoryById: state => id => state.categories.find(category => category.id == parseInt(id, 10)),

        roots: state => state.roots,

        nodes: state => state.nodes,

        lectureRoots: state => state.lectureRoots,

        lectureNodes: state => state.lectureNodes,

        getPathByLeafId: state => leafId => {
            let leaf = state.categories.find(category => category.id == parseInt(leafId, 10))
            let path = []

            if (leaf) {
                path.unshift(leaf)

                while (leaf.parent_id) {
                    leaf = state.categories.find(category => category.id == parseInt(leaf.parent_id, 10))
                    if (leaf) path.unshift(leaf)
                }
            }

            return path
        }
    },

    mutations: {
        storeCategories (state, categories) {
            state.categories = categories
        },

        storeRoots (state, categories) {
            state.roots = categories.filter(category => !category.parent_id).map(category => category.id)
        },

        storeNodes (state, categories) {
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

        storeLectureRoots (state, parentId) {
            state.lectureRoots = []
            state.lectureRoots.push(parentId)
        },

        storeLectureNodes (state, parentId) {
            const categories = []
            const nodes = {}

            let category = state.categories.find(cat => cat.id == parentId)
            categories.push(category)

            for (let i = 0; i < categories.length; i++) {
                if (categories[i].children.length) {
                    categories[i].children.forEach(child => {
                        category = state.categories.find(cat => cat.id == child.id)
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
    },

    actions: {
        fetchAllCategories({ commit }) {
            axios.get('/api/categories')
                .then(res => {
                    commit('storeCategories', res.data)

                    commit('storeNodes', res.data)
                    commit('storeRoots', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        fetchBranche({ commit }, parentId) {
            commit('storeLectureRoots', parentId)
            commit('storeLectureNodes', parentId)
        },
    }
}
