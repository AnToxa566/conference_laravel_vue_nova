import { createRouter, createWebHashHistory } from 'vue-router'
import store from '../store'


/* Error Components */
const NotFound = () => import('../views/ErrorPages/NotFound.vue')
/* Error Components */


/* Admin Components */
const ConferenceEdit = () => import('../views/Conference/ConferenceEdit.vue')
const ConferenceAdd = () => import('../views/Conference/ConferenceAdd.vue')

const Categories = () => import('../views/Category/Categories.vue')
const Meetings = () => import('../views/Meeting/Meetings.vue')
/* Admin Components */


/* Guest Components */
const Login = () => import('../views/Auth/Login.vue')
const Register = () => import('../views/Auth/Register.vue')
const Conferences = () => import('../views/Conference/Conferences.vue')
/* Guest Components */


/* Authenticated Components */
const UserProfile = () => import('../views/User/UserProfile.vue')

const ConferenceShow = () => import('../views/Conference/ConferenceShow.vue')

const Lectures = () => import('../views/Lecture/Lectures.vue')
const LectureShow = () => import('../views/Lecture/LectureShow.vue')
const LectureEdit = () => import('../views/Lecture/LectureEdit.vue')

const FavoriteLectures = () => import('../views/Lecture/FavoriteLectures.vue')
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


        /* User Components */
        {
            name: "profile",
            path: "/profile",
            component: UserProfile,
            meta: {
                middleware: "auth",
                title: `Profile`
            }
        },
        /* User Components */


        /* Conference Components */
        {
            name: 'conferences',
            path: '/',
            component: Conferences,
            meta: {
                middleware: "guest",
                title: `Conferences`,
                breadCrumb: 'conferences',
            },
        },
        {
            path: '/conferences/:id',
            name: 'conferenceShow',
            component: ConferenceShow,
            meta: {
                middleware: "auth",
                title: `Conference`,
                breadCrumb: 'conference',
            }
        },
        {
            path: '/conferences/add',
            name: 'conferenceAdd',
            component: ConferenceAdd,
            meta: {
                middleware: "admin",
                title: `Add conference`
            }
        },
        {
            path: '/conferences/:id/edit',
            name: 'conferenceEdit',
            component: ConferenceEdit,
            meta: {
                middleware: "admin",
                title: `Edit conference`
            }
        },
        /* Conference Components */


        /* Lecture Components */
        {
            path: '/conferences/:id/lectures',
            name: 'lectures',
            component: Lectures,
            meta: {
                middleware: "auth",
                title: `Lectures`,
                breadCrumb: 'conference',
            }
        },
        {
            path: '/conferences/:conference_id/lectures/:lecture_id',
            name: 'lectureShow',
            component: LectureShow,
            meta: {
                middleware: "auth",
                title: `Lecture`,
                breadCrumb: 'lecture',
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
        {
            path: '/favorite',
            name: 'favorite',
            component: FavoriteLectures,
            meta: {
                middleware: "auth",
                title: `Favorite Lectures`
            }
        },
        /* Lecture Components */


        /* Category Components */
        {
            path: '/categories',
            name: 'categories',
            component: Categories,
            meta: {
                middleware: "admin",
                title: `Categories`
            }
        },
        /* Category Components */


        /* Meeting Components */
        {
            path: '/meetings',
            name: 'meetings',
            component: Meetings,
            meta: {
                middleware: "admin",
                title: `Meetings`
            }
        },
        /* Meeting Components */
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
    else if (to.meta.middleware === "admin") {
        if (store.getters['auth/isAdmin']) {
            next()
        }
        else {
            next({ name: "404" })
        }
    }
    else if (to.meta.middleware === "guest") {
        if (store.state.auth.authenticated && (to.name === 'login' || to.name === 'register')) {
            next({ name: "conferences" })
        }
        else {
            next()
        }
    }
    else {
        next()
    }
})


export default router
