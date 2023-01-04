import axios from 'axios'

export default {
    namespaced: true,

    state: {
        countries: [],
    },

    getters: {
        countries(state) {
            return state.countries
        },
    },

    mutations: {
        SET_COUNTRIES (state, value) {
            state.countries = value
        },
    },

    actions: {
        fetchAllCountries({ commit }) {
            axios.get('/api/country')
                .then(res => {
                    commit('SET_COUNTRIES', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
