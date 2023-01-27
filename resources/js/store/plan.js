import axios from 'axios'
import planSlugs from '../config/plan_slugs'
import loadStatus from '../config/load_status'


export default {
    namespaced: true,

    state: {
        plans: [],
        plan: null,

        loadUserPlanStatus: loadStatus.NOT_STARTED,
    },

    getters: {
        planSlugs: () => planSlugs,

        plans: state => state.plans,

        plan: state => state.plan,

        loadUserPlanStatus: state => state.loadUserPlanStatus,
    },

    mutations: {
        storePlans(state, data) {
            state.plans = data
        },

        storePlan(state, data) {
            state.plan = data
        },

        storeLoadUserPlanStatus(state, data) {
            state.loadUserPlanStatus = data
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

        fetchUserPlan({ commit }) {
            commit('storeLoadUserPlanStatus', loadStatus.STARTED)

            axios.get(`/api/user/current-plan`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storePlan', res.data)
                    commit('storeLoadUserPlanStatus', loadStatus.LOADED)
                })
                .catch(err => {
                    console.log(err.response)
                    commit('storeLoadUserPlanStatus', loadStatus.FAILED)
                })
        },
    }
}
