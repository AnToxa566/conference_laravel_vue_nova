import { createRouter, createWebHashHistory } from 'vue-router'
import store from '../store'


/* Guest Components */
const NotFound = () => import('../views/NotFound.vue')
/* Guest Components */


/* Guest Components */
const Login = () => import('../views/Login.vue')
const Register = () => import('../views/Register.vue')
const Conferences = () => import('../views/Conferences.vue')
/* Guest Components */


/* Authenticated Components */
const ConferenceShow = () => import('../views/ConferenceShow.vue')
const ConferenceEdit = () => import('../views/ConferenceEdit.vue')
const ConferenceAdd = () => import('../views/ConferenceAdd.vue')

const Lectures = () => import('../views/Lectures.vue')
const LectureShow = () => import('../views/LectureShow.vue')
const LectureEdit = () => import('../views/LectureEdit.vue')
/* Authenticated Components */


const router = createRouter({
    history: createWebHashHistory(),
    routes: [
        /* 404 - Not Found */
        {
            name: '404',
            path: '/404',
            component: NotFound,
            meta: {
                middleware: "guest",
                title: `Not Found`
            }
        },
        {
            path: '/:pathMatch(.*)*',
            redirect: '/404',
        },
        /* 404 - Not Found */


        /* Auth Components */
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
        /* Auth Components */


        /* Conference Components */
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
        /* Conference Components */


        /* Lecture Components */
        {
            path: '/conferences/:conference_id/lectures',
            name: 'lectures',
            component: Lectures,
            meta: {
                middleware: "auth",
                title: `Lectures`
            }
        },
        {
            path: '/conferences/:conference_id/lectures/:lecture_id',
            name: 'lectureShow',
            component: LectureShow,
            meta: {
                middleware: "auth",
                title: `Lecture`
            }
        },
        {
            path: '/conferences/:conference_id/lectures/:lecture_id/edit',
            name: 'lectureEdit',
            component: LectureEdit,
            meta: {
                middleware: "auth",
                title: `Edit lecture`
            }
        },
        /* Lecture Components */
    ]
})


router.beforeEach((to, from, next) => {
    document.title = to.meta.title

    if (to.meta.middleware === "auth") {
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
