import { createRouter, createWebHashHistory } from 'vue-router'
import store from '../store'


/* Guest Component */
const Login = () => import('../views/Login.vue')
const Register = () => import('../views/Register.vue')
const Conferences = () => import('../views/Conferences.vue')
/* Guest Component */


/* Authenticated Component */
const ConferenceShow = () => import('../views/ConferenceShow.vue')
const ConferenceEdit = () => import('../views/ConferenceEdit.vue')
const ConferenceAdd = () => import('../views/ConferenceAdd.vue')
/* Authenticated Component */


const router = createRouter({
    history: createWebHashHistory(),
    routes: [
        {
            name: "login",
            path: "/login",
            component: Login,
            meta: {
                middleware: "guest",
                title: `Login`
            }
        },
        {
            name: "register",
            path: "/register",
            component: Register,
            meta: {
                middleware: "guest",
                title: `Register`
            }
        },
        {
            name: 'conferences',
            path: '/',
            component: Conferences,
            meta: {
                middleware: "guest",
                title: `Conferences`
            },
        },
        {
            path: '/conferences/:id',
            name: 'conferenceShow',
            component: ConferenceShow,
            meta: {
                middleware: "auth",
                title: `Conference`
            }
        },
        {
            path: '/conferences/add',
            name: 'conferenceAdd',
            component: ConferenceAdd,
            meta: {
                middleware: "auth",
                title: `Add conference`
            }
        },
        {
            path: '/conferences/:id/edit',
            name: 'conferenceEdit',
            component: ConferenceEdit,
            meta: {
                middleware: "auth",
                title: `Edit conference`
            }
        },
    ]
})


router.beforeEach((to, from, next) => {
    document.title = to.meta.title

    if (to.meta.middleware != "guest") {
        if (store.state.auth.authenticated) {
            next()
        }
        else {
            next({ name: "login" })
        }
    }
    else {
        next()
    }
})


export default router
