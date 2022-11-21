import { createStore } from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import auth from './auth'
import conference from './conference'

const store = createStore({
    plugins: [
        createPersistedState()
    ],
    modules: {
        auth,
        conference,
    }
})

export default store
