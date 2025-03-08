import { createRouter, createWebHistory } from 'vue-router'
import { useCookies } from 'vue3-cookies'

const Login = () => import('@/views/Auth/Login.vue')
const Register = () => import('@/views/Auth/Register.vue')
const StudentHome = () => import('@/views/Student/Home.vue')
const ArticleDetails = () => import('@/views/Student/ArticleDetails.vue')
const MyArticles = () => import('@/views/Student/MyArticles.vue')
const DraftArticles = () => import('@/views/Student/DraftArticles.vue')
const Notification = () => import('@/views/Student/Notification.vue')
const Settings = () => import('@/views/Settings.vue')

const studentRoutes = [
  {
    path: '/student/home',
    name: 'Student Home',
    component: StudentHome,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/student/my-articles',
    name: 'My Articles',
    component: MyArticles,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/articles/:id',
    name: 'getArticleDetails',
    component: ArticleDetails,
    meta: {
      // requiresAuth: true,
    },
  },
  {
    path: '/student/my-articles/draft',
    name: 'Draft Articles',
    component: DraftArticles,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/student/notifications',
    name: 'Student Notifications',
    component: Notification,
    meta: {
      // requiresAuth: true,
    },
  },
  {
    path: '/student/settings',
    name: 'Settings',
    component: Settings,
    meta: {
      requiresAuth: true,
    },
  },
]

const authRoutes = [
  { path: '/auth/login', name: 'login', component: Login },
  { path: '/auth/forgot-password', name: 'forgot-password', component: Login },
  { path: '/auth/register', name: 'register', component: Register },
  { path: '/auth/logout', name: 'logout', component: Login },
]

// Wildcard route to catch undefined paths and redirect to login
const wildcardRoute = { path: '/:pathMatch(.*)*', redirect: '/auth/login' }

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [...studentRoutes, ...authRoutes, wildcardRoute],
})

router.beforeEach((to, from, next) => {
  const { cookies } = useCookies()
  const token = cookies.get('token')

  if (
    token &&
    (to.path === '/auth/login' ||
      to.path === '/auth/register' ||
      to.path === '/auth/forgot-password')
  ) {
    console.log(token)
    // If user is already authenticated and tries to access login/register, redirect to home
    next({ path: '/student/home', replace: true })
  } else if (to.meta.requiresAuth && !token) {
    // If route requires auth and user is not authenticated, redirect to login
    next({ path: '/auth/login', replace: true })
  } else {
    // Otherwise, proceed as normal
    next()
  }
})

export default router
