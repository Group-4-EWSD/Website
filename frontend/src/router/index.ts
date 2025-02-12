import { createRouter, createWebHistory } from 'vue-router'

const Home = () => import('@/views/Home.vue')
const Login = () => import('@/views/Auth/Login.vue')
const Register = () => import('@/views/Auth/Register.vue')
const NotFound = () => import('@/views/NotFound.vue')

const publicRoutes = [{ path: '/', name: 'home', component: Home }]

const authRoutes = [
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
]

const fallbackRoutes = [{ path: '/:pathMatch(.*)*', name: 'not-found', component: NotFound }]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [...publicRoutes, ...authRoutes, ...fallbackRoutes],
import { useCookies } from 'vue3-cookies'

const Home = () => import('@/views/Home.vue')
const Login = () => import('@/views/Auth/Login.vue')
const Register = () => import('@/views/Auth/Register.vue')

const publicRoutes = [
  {
    path: '/',
    name: 'home',
    component: Home,
    meta: {
      requiresAuth: true,
    },
  },
]

const authRoutes = [
  { path: '/auth/login', name: 'login', component: Login },
  { path: '/auth/forgot-password', name: 'forgot-password', component: Login },
  { path: '/auth/register', name: 'register', component: Register },
]

// Wildcard route to catch undefined paths and redirect to login
const wildcardRoute = { path: '/:pathMatch(.*)*', redirect: '/auth/login' }

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [...publicRoutes, ...authRoutes, wildcardRoute],
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
    // If user is already authenticated and tries to access login/register, redirect to home
    next({ path: '/', replace: true })
  } else if (to.meta.requiresAuth && !token) {
    // If route requires auth and user is not authenticated, redirect to login
    next({ path: '/auth/login', replace: true })
  } else {
    // Otherwise, proceed as normal
    next()
  }
})

export default router
