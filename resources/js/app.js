
// Bootstrap
import './bootstrap';
import '../sass/app.scss'


// Vuex
import store from './store'


// Vue-router
import router from './router'


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


// Vue Datepicker
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'


// Vue
import { createApp } from 'vue';
import App from './App.vue'


const app = createApp(App)


app
    .component('Datepicker', Datepicker)
    .use(vuetify)
    .use(store)
    .use(router)
    .mount('#app')
