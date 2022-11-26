
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


// Vue Social Sharing
import VueSocialSharing from 'vue-social-sharing'


// Vue Google Maps
import VueGoogleMaps from '@fawmi/vue-google-maps'


// Phone Number Input
import MazPhoneNumberInput from 'maz-ui/components/MazPhoneNumberInput'
import 'maz-ui/css/main.css'


// Vue
import { createApp } from 'vue';
import App from './App.vue'


const app = createApp(App)

app
    .component('Datepicker', Datepicker)
    .component('MazPhoneNumberInput', MazPhoneNumberInput)
    .use(vuetify)
    .use(store)
    .use(router)
    .use(VueSocialSharing)
    .use(VueGoogleMaps, {
        load: {
            key: import.meta.env.VITE_PUSHER_GOOGLE_MAPS_API_KEY,
        },
    }).mount('#app')
