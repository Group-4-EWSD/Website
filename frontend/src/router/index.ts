import { createRouter, createWebHistory } from 'vue-router'

import { getCookie } from '@/lib/utils'
import { useUserStore } from '@/stores/user'
import Unauthorized from '@/views/Unauthorized.vue'

const Login = () => import('@/views/Auth/Login.vue')
const Register = () => import('@/views/Auth/Register.vue')
const PasswordReset = () => import('@/views/Auth/PasswordReset.vue')

const StudentHome = () => import('@/views/Student/Home.vue')
const ArticleDetails = () => import('@/views/Student/ArticleDetails.vue')
const MyArticles = () => import('@/views/Student/MyArticles.vue')
const DraftArticles = () => import('@/views/Student/DraftArticles.vue')

const CoordinatorDashboard = () => import('@/views/Coordinator/Dashboard.vue')
const CoordinatorArticles = () => import('@/views/Coordinator/Articles.vue')

const Notification = () => import('@/views/Notification.vue')
const Settings = () => import('@/views/Settings.vue')

const studentRoutes = [
  {
    path: '/student/home',
    name: 'Student Home',
    component: StudentHome,
    meta: {
      requiresAuth: true,
      roles: ['student'],
    },
  },
  {
    path: '/student/my-articles',
    name: 'My Articles',
    component: MyArticles,
    meta: {
      requiresAuth: true,
      roles: ['student'],
    },
  },
  {
    path: '/student/my-articles/draft',
    name: 'Draft Articles',
    component: DraftArticles,
    meta: {
      requiresAuth: true,
      roles: ['student'],
    },
  },
]

const coordinatorRoutes = [
  {
    path: '/coordinator/dashboard',
    name: 'Coordinator Dashboard',
    component: CoordinatorDashboard,
    meta: {
      // requiresAuth: true,
      roles: ['Marketing Coordinator'],
    },
  },
  {
    path: '/coordinator/articles',
    name: 'Articles',
    component: CoordinatorArticles,
    meta: {
      // requiresAuth: true,
      roles: ['Marketing Coordinator'],
    },
  },
]

const commomRoutes = [
  {
    path: '/articles/:id',
    name: 'getArticleDetails',
    component: ArticleDetails,
    meta: {
      requiresAuth: true,
      roles: ['Student', 'Marketing Coordinator'],
    },
  },
  {
    path: '/notifications',
    name: 'Notification',
    component: Notification,
    meta: {
      // requiresAuth: true,
      roles: ['Student', 'Marketing Coordinator'],
    },
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: {
      // requiresAuth: true,
      roles: ['Student', 'Marketing Coordinator'],
    },
  },
]

const authRoutes = [
  { path: '/auth/login', name: 'login', component: Login },
  { path: '/auth/forgot-password', name: 'forgot-password', component: PasswordReset },
  { path: '/auth/register', name: 'register', component: Register },
  { path: '/auth/logout', name: 'logout', component: Login },
]

const fallbackRoutes = [{ path: '/unauthorized', name: 'Unauthorized', component: Unauthorized }]

// Wildcard route to catch undefined paths and redirect to login
const wildcardRoute = { path: '/:pathMatch(.*)*', redirect: '/auth/login' }

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    ...studentRoutes,
    ...coordinatorRoutes,
    ...commomRoutes,
    ...authRoutes,
    ...fallbackRoutes,
    wildcardRoute,
  ],
})

router.beforeEach((to, from, next) => {
  const token = getCookie('token')
  const userInfo = getCookie('user')

  const userStore = useUserStore()
  userStore.setUser(userInfo)
  const userType = userStore.user?.user_type_name?.trim() || ''

  if (
    token &&
    userInfo &&
    ['/auth/login', '/auth/register', '/auth/forgot-password'].includes(to.path)
  ) {
    console.log(token + '' + userType)
    // If user is already authenticated and tries to access login/register, redirect to home
    const redirectRoutes: Record<string, string> = {
      Student: '/student/home',
      'Marketing Coordinator': '/coordinator/dashboard',
    }

    return next({ path: redirectRoutes[userType] ?? '/', replace: true })
  } else if (Array.isArray(to.meta.roles) && !to.meta.roles.includes(userType)) {
    console.log(userType)
    // If user does not have permission to access the route, redirect to unauthorize page
    next({ path: '/unauthorized', replace: true })
  } else if (to.meta.requiresAuth && !token) {
    // If route requires auth and user is not authenticated, redirect to login
    next({ path: '/auth/login', replace: true })
  } else {
    // Otherwise, proceed as normal
    next()
  }
})

export default router
