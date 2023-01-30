import axios from 'axios'
import planSlugs from '../config/plan_slugs'
import loadStatuses from '../config/load_statuses'


export default {
    namespaced: true,

    state: {
        plans: [],
        plan: null,

        loadPlansStatus: loadStatuses.NOT_STARTED,
        loadUserPlanStatus: loadStatuses.NOT_STARTED,
    },

    getters: {
        planSlugs: () => planSlugs,

        plans: state => state.plans,

        plan: state => state.plan,

        loadStatuses: () => loadStatuses,

        loadPlansStatus: state => state.loadPlansStatus,

        loadUserPlanStatus: state => state.loadUserPlanStatus,
    },

    mutations: {
        storePlans(state, data) {
            state.plans = data
        },

        storePlan(state, data) {
            state.plan = data
        },

        storeLoadPlansStatus(state, data) {
            state.loadPlansStatus = data
        },

        storeLoadUserPlanStatus(state, data) {
            state.loadUserPlanStatus = data
        },
    },

    actions: {
        fetchPlans({ commit }) {
            commit('storeLoadPlansStatus', loadStatuses.STARTED)

            axios.get(`/api/plans`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storePlans', res.data)
                    commit('storeLoadPlansStatus', loadStatuses.LOADED)
                })
                .catch(err => {
                    console.log(err.response)
                    commit('storeLoadPlansStatus', loadStatuses.FAILED)
                })
        },

        fetchUserPlan({ commit }) {
            commit('storeLoadUserPlanStatus', loadStatuses.STARTED)

            axios.get(`/api/user/current-plan`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storePlan', res.data)
                    commit('storeLoadUserPlanStatus', loadStatuses.LOADED)
                })
                .catch(err => {
                    console.log(err.response)
                    commit('storeLoadUserPlanStatus', loadStatuses.FAILED)
                })
        },
    }
}
