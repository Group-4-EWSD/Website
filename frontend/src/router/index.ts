import { createRouter, createWebHistory } from 'vue-router'
import { useCookies } from 'vue3-cookies'
import MyArticles from '@/views/Student/MyArticles.vue'
import Home from '@/views/Student/Home.vue'
import Login from '@/views/Auth/Login.vue'
import Register from '@/views/Auth/Register.vue'
import ArticleDetails from '@/views/Student/ArticleDetails.vue'

const studentRoutes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: {
      // requiresAuth: true,
    },
  },
  {
    path: '/student/my-articles',
    name: 'My Articles',
    component: MyArticles,
    meta: {
      // requiresAuth: true,
    },
  },
  {
    path: '/articles/:id',
    name: 'ArticleDetails',
    component: ArticleDetails,
    meta: {
      // requiresAuth: true,
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
