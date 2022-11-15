
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
import ConferenceEdit from './views/ConferenceEdit.vue'
import ConferenceAdd from './views/ConferenceAdd.vue'
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
        {
            path: '/conferences/add',
            name: 'ConferenceAdd',
            component: ConferenceAdd,
        },
        {
            path: '/conferences/:id/edit',
            name: 'ConferenceEdit',
            component: ConferenceEdit,
        },
    ]
})


// Vue Datepicker
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'


// App ussing
app
    .component('Datepicker', Datepicker)
    .use(vuetify)
    .use(router)
    .mount('#app')
