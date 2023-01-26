import { createStore } from 'vuex'
import createPersistedState from 'vuex-persistedstate'

/* Stores Import */
import auth from './auth'

import category from './category'
import comment from './comment'
import conference from './conference'
import country from './country'

import favorite from './favorite'

import lecture from './lecture'

import meeting from './meeting'

import payment from './payment'
import plan from './plan'

import user_conferences from './user_conferences'
/* Stores Import */


const store = createStore({
    plugins: [
        createPersistedState()
    ],
    modules: {
        auth,
        category,
        comment,
        conference,
        country,
        favorite,
        lecture,
        meeting,
        payment,
        plan,
        user_conferences,
    }
})

export default store
