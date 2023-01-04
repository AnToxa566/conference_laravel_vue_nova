import { createStore } from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import auth from './auth'
import storage from './storage'
import comment from './comment'
import lecture from './lecture'
import favorite from './favorite'
import category from './category'
import conference from './conference'
import user_conferences from './user_conferences'

const store = createStore({
    plugins: [
        createPersistedState()
    ],
    modules: {
        auth,
        storage,
        lecture,
        comment,
        favorite,
        category,
        conference,
        user_conferences,
    }
})

export default store
