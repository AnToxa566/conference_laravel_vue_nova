import { createStore } from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import auth from './auth'
import conference from './conference'
import user_conferences from './user_conferences'

const store = createStore({
    plugins: [
        createPersistedState()
    ],
    modules: {
        auth,
        conference,
        user_conferences,
    }
})

export default store
