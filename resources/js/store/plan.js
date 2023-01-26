import axios from 'axios'

export default {
    namespaced: true,

    state: {
        plans: [],
        plan: null,
    },

    getters: {
        plans: state => state.plans,
    },

    mutations: {
        storePlans(state, data) {
            state.plans = data
        },

        storePlan(state, data) {
            state.plan = data
        },
    },

    actions: {
        fetchPlans({ commit }) {
            axios.get(`/api/plans`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storePlans', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        fetchPlanDetail({ commit }, slug) {
            axios.get(`/api/plans/${slug}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storePlan', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
