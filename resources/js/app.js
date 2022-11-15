
// Bootstrap
import './bootstrap';


// Vue
import { createApp } from 'vue';
import App from './App.vue'

const app = createApp(App)


// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import { md3 } from 'vuetify/blueprints'
import '@mdi/font/css/materialdesignicons.css'

const vuetify = createVuetify({
    theme: {
        defaultTheme: 'dark'
    },
    blueprint: md3,
    icons: {
        defaultSet: 'mdi',
    },
})


// Vue-Router
import { createRouter, createWebHashHistory } from 'vue-router'
import ConferenceShow from './views/ConferenceShow.vue'
import Conferences from './views/Conferences.vue'
import Home from './views/Home.vue'

const router = createRouter({
    history: createWebHashHistory(),
    routes: [
        {
            path: '/',
            name: 'Home',
            component: Home,
        },
        {
            path: '/conferences',
            name: 'Conferences',
            component: Conferences,
        },
        {
            path: '/conferences/:id',
            name: 'ConferenceShow',
            component: ConferenceShow,
        },
    ]
})


// App ussing
app
    .use(vuetify)
    .use(router)
    .mount('#app')
