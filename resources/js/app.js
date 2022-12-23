
// Bootstrap
import './bootstrap'
import '../sass/app.scss'
import '../css/app.css'


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


// Vue Treeview
import Tree from "vue3-treeview";
import "vue3-treeview/dist/style.css";


// My UI Components
import MyAppBar from './components/UI/MyAppBar.vue'
import BreadCrumb from './components/UI/BreadCrumb.vue'
import MyHeader from './components/UI/MyHeader.vue'
import MyInfoCard from './components/UI/MyInfoCard.vue'
import MyJoinCancelButtons from './components/UI/MyJoinCancelButtons.vue'
import MyShareButtons from './components/UI/MyShareButtons.vue'
import FavoriteBtn from './components/UI/FavoriteBtn.vue'


// My Form Components
import CustomErrorAlert from './components/UI/CustomErrorAlert.vue'
import CustomInput from './components/FormComponents/CustomInput.vue'
import CustomPhoneNumberInput from './components/FormComponents/CustomPhoneNumberInput.vue'
import CustomDatepicker from './components/FormComponents/CustomDatepicker.vue'
import CustomTimepicker from './components/FormComponents/CustomTimepicker.vue'
import CountrySelected from './components/FormComponents/CountrySelected.vue'
import CategorySelected from './components/FormComponents/CategorySelected.vue'


// My Filter Components
import ConferenceFilterNavigation from './components/Filter/ConferenceFilterNavigation.vue'
import LectureFilterNavigation from './components/Filter/LectureFilterNavigation.vue'
import FilterMultipleSelects from './components/Filter/FilterMultipleSelects.vue'
import FilterRangeSlider from './components/Filter/FilterRangeSlider.vue'
import FilterDate from './components/Filter/FilterDate.vue'
import FilterTime from './components/Filter/FilterTime.vue'


// My Skeleton Components
import ConferenceItemSkeleton from './components/Skeleton/ConferenceItemSkeleton.vue'


// Vue
import { createApp } from 'vue';
import App from './App.vue'


const app = createApp(App)

app
    .component('Tree', Tree)
    .component('Datepicker', Datepicker)
    .component('QuillEditor', QuillEditor)
    .component('MazPhoneNumberInput', MazPhoneNumberInput)

    .component('FavoriteBtn', FavoriteBtn)
    .component('CustomInput', CustomInput)
    .component('CustomErrorAlert', CustomErrorAlert)
    .component('CustomDatepicker', CustomDatepicker)
    .component('CustomTimepicker', CustomTimepicker)
    .component('CustomPhoneNumberInput', CustomPhoneNumberInput)
    .component('CountrySelected', CountrySelected)
    .component('CategorySelected', CategorySelected)

    .component('MyAppBar', MyAppBar)
    .component('BreadCrumb', BreadCrumb)
    .component('MyHeader', MyHeader)
    .component('MyInfoCard', MyInfoCard)
    .component('MyJoinCancelButtons', MyJoinCancelButtons)
    .component('MyShareButtons', MyShareButtons)

    .component('ConferenceFilterNavigation', ConferenceFilterNavigation)
    .component('LectureFilterNavigation', LectureFilterNavigation)
    .component('FilterMultipleSelects', FilterMultipleSelects)
    .component('FilterRangeSlider', FilterRangeSlider)
    .component('FilterDate', FilterDate)
    .component('FilterTime', FilterTime)

    .component('ConferenceItemSkeleton', ConferenceItemSkeleton)

    .use(vuetify)
    .use(store)
    .use(router)
    .use(VueSocialSharing)

    .use(VueGoogleMaps, {
        load: {
            key: import.meta.env.VITE_PUSHER_GOOGLE_MAPS_API_KEY,
        },

    }).mount('#app')
