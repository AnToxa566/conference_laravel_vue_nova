
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


// Vue Quill (WYSIWYG Redactor)
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';


// My Components
import CustomFavorites from './components/UI/CustomFavorites.vue'
import MyAppBar from './components/UI/MyAppBar.vue'
import MyHeader from './components/UI/MyHeader.vue'
import MyInfoCard from './components/UI/MyInfoCard.vue'
import MyJoinCancelButtons from './components/UI/MyJoinCancelButtons.vue'
import MyShareButtons from './components/UI/MyShareButtons.vue'


// Vue
import { createApp } from 'vue';
import App from './App.vue'


const app = createApp(App)

app
    .component('Datepicker', Datepicker)
    .component('QuillEditor', QuillEditor)
    .component('MazPhoneNumberInput', MazPhoneNumberInput)
    .component('CustomFavorites', CustomFavorites)
    .component('MyAppBar', MyAppBar)
    .component('MyHeader', MyHeader)
    .component('MyInfoCard', MyInfoCard)
    .component('MyJoinCancelButtons', MyJoinCancelButtons)
    .component('MyShareButtons', MyShareButtons)
    .use(vuetify)
    .use(store)
    .use(router)
    .use(VueSocialSharing)
    .use(VueGoogleMaps, {
        load: {
            key: import.meta.env.VITE_PUSHER_GOOGLE_MAPS_API_KEY,
        },
    }).mount('#app')
