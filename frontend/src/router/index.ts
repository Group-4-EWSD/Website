import { createRouter, createWebHistory } from 'vue-router'

import { getCookie } from '@/lib/utils'
import { useUserStore } from '@/stores/user'

const Login = () => import('@/views/Auth/Login.vue')
const Register = () => import('@/views/Auth/Register.vue')
const StudentHome = () => import('@/views/Student/Home.vue')
const ArticleDetails = () => import('@/views/Student/ArticleDetails.vue')
const MyArticles = () => import('@/views/Student/MyArticles.vue')
const DraftArticles = () => import('@/views/Student/DraftArticles.vue')
const Notification = () => import('@/views/Student/Notification.vue')
const Settings = () => import('@/views/Settings.vue')

const CoordinatorDashboard = () => import('@/views/Coordinator/Dashboard.vue')
const CoordinatorNotification = () => import('@/views/Coordinator/Notification.vue')
const CoordinatorSettings = () => import('@/views/Coordinator/Settings.vue')
const CoordinatorArticles = () => import('@/views/Coordinator/Articles.vue')

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
      requiresAuth: true,
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
      requiresAuth: true,
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

const coordinatorRoutes = [
  {
    path: '/coordinator/dashboard',
    name: 'Coordinator Dashboard',
    component: CoordinatorDashboard,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/coordinator/articles',
    name: 'Articles',
    component: CoordinatorArticles,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/coordinator/notifications',
    name: 'Coordinator Notification',
    component: CoordinatorNotification,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: '/coordinator/settings',
    name: 'Coordinator Settings',
    component: CoordinatorSettings,
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
  routes: [...studentRoutes, ...coordinatorRoutes, ...authRoutes, wildcardRoute],
})

router.beforeEach((to, from, next) => {
  const token = getCookie('token')
  const userInfo = getCookie('user')

  const userStore = useUserStore()
  userStore.setUser(userInfo)

  if (
    token &&
    userInfo &&
    (to.path === '/auth/login' ||
      to.path === '/auth/register' ||
      to.path === '/auth/forgot-password')
  ) {
    // cookies.remove('token')
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
