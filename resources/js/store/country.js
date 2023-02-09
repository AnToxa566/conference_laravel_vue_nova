import axios from 'axios'

export default {
    namespaced: true,

    state: {
        countries: [],
    },

    getters: {
        countries: state => state.countries,
    },

    mutations: {
        storeCountries (state, value) {
            state.countries = value
        },
    },

    actions: {
        fetchAllCountries({ commit }) {
            axios.get('/api/countries')
                .then(res => {
                    commit('storeCountries', res.data)
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
