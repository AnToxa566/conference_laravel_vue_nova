import axios from 'axios'
import loadStatuses from '../config/load_statuses'

export default {
    namespaced: true,

    state: {
        intentToken: '',

        paymentMethods: [],

        paymentMethodsLoadStatus: loadStatuses.NOT_STARTED,
    },

    getters: {
        intentToken: state => state.intentToken,

        paymentMethods: state => state.paymentMethods,

        paymentMethodsLoadStatus: state => state.paymentMethodsLoadStatus,
    },

    mutations: {
        storeIntentToken(state, data) {
            state.intentToken = data
        },

        storePaymentMethods(state, data) {
            state.paymentMethods = data
        },

        storePaymentMethodsLoadStatus(state, data) {
            state.paymentMethodsLoadStatus = data
        },
    },

    actions: {
        loadIntent({ commit }) {
            axios.get('/api/user/setup-intent', JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storeIntentToken', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        savePaymentMethod({ dispatch }, method) {
            axios.post('/api/user/payments', { payment_method: method }, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    dispatch('loadPaymentMethods')
                })
                .catch(err => {
                    console.log(err.response)
                })
        },

        loadPaymentMethods({ commit }) {
            commit('storePaymentMethodsLoadStatus', loadStatuses.STARTED)

            axios.get('/api/user/payment-methods', JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    commit('storePaymentMethods', res.data)
                    commit('storePaymentMethodsLoadStatus', loadStatuses.COMPLETED)
                })
                .catch(err => {
                    console.log(err.response)
                    commit('storePaymentMethodsLoadStatus', loadStatuses.FAILED)
                })
        },

        removePaymentMethod({ dispatch }, paymentID) {
            axios.post('/api/user/remove-payment', { id: paymentID }, JSON.parse(localStorage.getItem('config')))
                .then(res => {
                    dispatch('loadPaymentMethods')
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
