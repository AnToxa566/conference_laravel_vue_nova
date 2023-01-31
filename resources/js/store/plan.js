import axios from 'axios'
import planSlugs from '../config/plan_slugs'
import loadStatuses from '../config/load_statuses'


export default {
    namespaced: true,

    state: {
        plans: [],

        currentPlan: {},
        plan: {},

        loadPlanStatus: loadStatuses.NOT_STARTED,
        loadPlansStatus: loadStatuses.NOT_STARTED,
        loadUserPlanStatus: loadStatuses.NOT_STARTED,

        updateSubscriptionStatus: loadStatuses.NOT_STARTED,
    },

    getters: {
        planSlugs: () => planSlugs,

        loadStatuses: () => loadStatuses,


        plans: state => state.plans,


        currentPlan: state => state.currentPlan,

        plan: state => state.plan,


        loadPlanStatus: state => state.loadPlansStatus,

        loadPlansStatus: state => state.loadPlansStatus,

        loadUserPlanStatus: state => state.loadUserPlanStatus,


        updateSubscriptionStatus: state => state.updateSubscriptionStatus,
    },

    mutations: {
        storePlans(state, data) {
            state.plans = data
        },


        storePlan(state, data) {
            state.plan = data
        },

        storeCurrentPlan(state, data) {
            state.currentPlan = data
        },


        storeLoadPlanStatus(state, data) {
            state.loadPlansStatus = data
        },

        storeLoadPlansStatus(state, data) {
            state.loadPlansStatus = data
        },

        storeLoadUserPlanStatus(state, data) {
            state.loadUserPlanStatus = data
        },


        storeUpdateSubscriptionStatus(state, data) {
            state.updateSubscriptionStatus = data
        },
    },

    actions: {
        fetchPlans({ commit }) {
            commit('storeLoadPlansStatus', loadStatuses.STARTED)

            axios.get(`/api/plans`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storePlans', res.data)
                    commit('storeLoadPlansStatus', loadStatuses.COMPLETED)
                })
                .catch(err => {
                    console.log(err.response)
                    commit('storeLoadPlansStatus', loadStatuses.FAILED)
                })
        },

        fetchPlan({ commit }, slug) {
            commit('storeLoadPlanStatus', loadStatuses.STARTED)

            axios.get(`/api/plans/${slug}`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storePlan', res.data)
                    commit('storeLoadPlanStatus', loadStatuses.COMPLETED)
                })
                .catch(err => {
                    console.log(err.response)
                    commit('storeLoadPlanStatus', loadStatuses.FAILED)
                })
        },

        fetchUserPlan({ commit }) {
            commit('storeLoadUserPlanStatus', loadStatuses.STARTED)

            axios.get(`/api/user/current-plan`, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storeCurrentPlan', res.data)
                    commit('storeLoadUserPlanStatus', loadStatuses.COMPLETED)
                })
                .catch(err => {
                    console.log(err.response)
                    commit('storeLoadUserPlanStatus', loadStatuses.FAILED)
                })
        },

        updateSubscription({ commit, dispatch }, query) {
            commit('storeUpdateSubscriptionStatus', loadStatuses.STARTED)

            axios.put('/api/plans/subscription', query, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storeUpdateSubscriptionStatus', loadStatuses.COMPLETED)
                    dispatch('fetchUserPlan')
                })
                .catch(err => {
                    console.log(err.response)
                    commit('storeUpdateSubscriptionStatus', loadStatuses.FAILED)
                })
        },
    }
}
