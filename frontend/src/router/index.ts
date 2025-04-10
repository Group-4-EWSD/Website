import { createRouter, createWebHistory } from 'vue-router'

import { getCookie } from '@/lib/utils'
import { useUserStore } from '@/stores/user'
import Unauthorized from '@/views/Unauthorized.vue'
import { visitCount } from '@/api/auth'

const Login = () => import('@/views/Auth/Login.vue')
const Register = () => import('@/views/Auth/Register.vue')
const PasswordReset = () => import('@/views/Auth/PasswordReset.vue')

const StudentHome = () => import('@/views/Student/Home.vue')
const ArticleDetails = () => import('@/views/ArticleDetails.vue')
const MyArticles = () => import('@/views/Student/MyArticles.vue')
const DraftArticles = () => import('@/views/Student/DraftArticles.vue')

const CoordinatorDashboard = () => import('@/views/Coordinator/Dashboard.vue')
const CoordinatorArticles = () => import('@/views/Coordinator/Articles.vue')

const ManagerDashboard = () => import('@/views/Manager/Dashboard.vue')
const ManagerArticles = () => import('@/views/Manager/Articles.vue')

const Notification = () => import('@/views/Notification.vue')
const Settings = () => import('@/views/Settings.vue')

const AdminManagement = () => import('@/views/Admin/Management.vue')
const AdminReports = () => import('@/views/Admin/Reports.vue')
const AdminUsers = () => import('@/views/Admin/Users.vue')
const ContactUs = () => import('@/views/Admin/ContactUs.vue')

const studentRoutes = [
  {
    path: '/student/home',
    name: 'Student Home',
    component: StudentHome,
    meta: {
      id: 1,
      roles: ['student'],
    },
  },
  {
    path: '/student/my-articles',
    name: 'My Articles',
    component: MyArticles,
    meta: {
      id: 2,
      roles: ['student'],
    },
  },
  {
    path: '/student/my-articles/draft',
    name: 'Draft Articles',
    component: DraftArticles,
    meta: {
      id: 5,
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
      id: 8,
      roles: ['Marketing Coordinator'],
    },
  },
  {
    path: '/coordinator/articles',
    name: 'Coordinator Articles',
    component: CoordinatorArticles,
    meta: {
      id: 9,
      roles: ['Marketing Coordinator'],
    },
  },
]

const managerRoutes = [
  {
    path: '/manager/dashboard',
    name: 'Manager Dashboard',
    component: ManagerDashboard,
    meta: {
      id: 10,
      roles: ['Marketing Manager'],
    },
  },
  {
    path: '/manager/articles',
    name: 'Articles',
    component: ManagerArticles,
    meta: {
      id: 14,
      roles: ['Marketing Manager'],
    },
  },
]

const commomRoutes = [
  {
    path: '/articles/:id',
    name: 'getArticleDetails',
    component: ArticleDetails,
    meta: {
      roles: ['Student', 'Marketing Coordinator'],
    },
  },
  {
    path: '/notifications',
    name: 'Notification',
    component: Notification,
    meta: {
      roles: ['Student', 'Marketing Coordinator'],
    },
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: {
      roles: ['Student', 'Marketing Coordinator', 'Admin', 'Marketing Manager', 'Guest'],
    },
  },
]

const adminRoutes = [
  {
    path: '/admin/management',
    name: 'Management',
    component: AdminManagement,
    meta: {
      id: 17,
      roles: ['Admin'],
    },
  },
  {
    path: '/admin/reports',
    name: 'Reports',
    component: AdminReports,
    meta: {
      id: 18,
      roles: ['Admin'],
    },
  },
  {
    path: '/admin/users',
    name: 'Users',
    component: AdminUsers,
    meta: {
      id: 20,
      roles: ['Admin'],
    },
  },
  {
    path: '/admin/contact-us',
    name: 'Contact Us',
    component: ContactUs,
    meta: {
      id: 21,
      roles: ['Admin'],
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

const allValidRoutes = [...studentRoutes, ...coordinatorRoutes, ...managerRoutes, ...adminRoutes]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [...allValidRoutes, ...commomRoutes, ...authRoutes, ...fallbackRoutes, wildcardRoute],
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
      'Marketing Manager': '/manager/dashboard',
      Admin: '/admin/management',
      Guest: '/settings',
    }

    return next({ path: redirectRoutes[userType] ?? '/', replace: true })
  } else if (to.meta.requiresAuth && !token) {
    // If route requires auth and user is not authenticated, redirect to login
    next({ path: '/auth/login', replace: true })
  } else if (Array.isArray(to.meta.roles) && !to.meta.roles.includes(userType)) {
    console.log(userType)
    // If user does not have permission to access the route, redirect to unauthorize page
    // next({ path: '/unauthorized', replace: true })
    next()
  } else {
    // Otherwise, proceed as normal
    next()
  }
})

// Add afterEach hook to track page visits after navigation is complete
router.afterEach((to) => {
  // Don't track visits to authentication pages
  if (!to.path.startsWith('/auth/')) {
    trackPageVisit(to.path)
  }
})

// Function to track page visits
function trackPageVisit(path: string) {
  // const userInfo = getCookie('user')
  // // Get user ID if available
  // const userId = userInfo ? JSON.parse(userInfo).id : 'anonymous';

  const pageInfo = allValidRoutes.find((route) => route.path === path)
  if (pageInfo) {
    visitCount(pageInfo.meta.id.toString())
  } else if (commomRoutes.find((route) => route.path === path)) {
    // if common route, check with role and set id accordingly
    const userStore = useUserStore()
    const userType = userStore.user?.user_type_name?.trim() || ''

    const roleAndPageMap: Record<string, Record<string, number>> = {
      Student: {
        '/articles/:id': 3,
        '/notifications': 6,
        '/settings': 7,
      },
      'Marketing Coordinator': {
        '/articles/:id': 10,
        '/notifications': 11,
        '/settings': 12,
      },
      'Marketing Manager': {
        '/notifications': 15,
        '/settings': 16,
      },
      Admin: {
        '/settings': 19,
      },
      Guest: {
        '/settings': 21,
      },
    }

    const pageId = roleAndPageMap[userType]?.[path] || 0
    if (pageId) {
      visitCount(pageId.toString())
    }
  }
}

export default router
