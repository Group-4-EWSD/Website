import { createRouter, createWebHistory } from 'vue-router'

import { visitCount } from '@/api/auth'
import { getCookie } from '@/lib/utils'
import { useUserStore } from '@/stores/user'
import Unauthorized from '@/views/Unauthorized.vue'

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

const Home = () => import('@/views/Home.vue')
const AboutUs = () => import('@/views/AboutUs.vue')
const ContactUsForm = () => import('@/views/ContactUsForm.vue')
const TermsAndConditions = () => import('@/views/TermsAndConditions.vue')

const studentRoutes = [
  {
    path: '/student/home',
    name: 'Student Home',
    component: StudentHome,
    meta: {
      id: 1, // Student Dashboard Page
      roles: ['student'],
    },
  },
  {
    path: '/student/my-articles',
    name: 'My Articles',
    component: MyArticles,
    meta: {
      id: 4, // Student My Articles Page
      roles: ['student'],
    },
  },
  {
    path: '/student/my-articles/draft',
    name: 'Draft Articles',
    component: DraftArticles,
    meta: {
      id: 5, // Student Draft Articles Page
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
      id: 8, // Marketing Coordinator Dashboard Page
      roles: ['Marketing Coordinator'],
    },
  },
  {
    path: '/coordinator/articles',
    name: 'Coordinator Articles',
    component: CoordinatorArticles,
    meta: {
      id: 9, // Marketing Coordinator Articles Page
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
      id: 11, // Marketing Manager Dashboard Page
      roles: ['Marketing Manager'],
    },
  },
  {
    path: '/manager/articles',
    name: 'Articles',
    component: ManagerArticles,
    meta: {
      id: 12, // Marketing Manager Articles Page
      roles: ['Marketing Manager'],
    },
  },
]

const commomRoutes = [
  {
    path: '/home',
    name: 'Home',
    component: Home,
    meta: {
      public: true,
      roles: [],
    },
  },
  {
    path: '/about',
    name: 'About Us',
    component: AboutUs,
    meta: {
      public: true,
      roles: [],
    },
  },
  {
    path: '/contact-us',
    name: 'Contact Us Form',
    component: ContactUsForm,
    meta: {
      public: true,
      roles: [],
    },
  },
  {
    path: '/terms-and-conditions',
    name: 'Terms and Conditions',
    component: TermsAndConditions,
    meta: {
      public: true,
      roles: [],
    },
  },
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
      id: 13, // Admin Dashboard Page
      roles: ['Admin'],
    },
  },
  {
    path: '/admin/reports',
    name: 'Reports',
    component: AdminReports,
    meta: {
      id: 14, // Admin Reports Page
      roles: ['Admin'],
    },
  },
  {
    path: '/admin/users',
    name: 'Users',
    component: AdminUsers,
    meta: {
      id: 17, // Admin User Page
      roles: ['Admin'],
    },
  },
  {
    path: '/admin/contact-us',
    name: 'Contact Us',
    component: ContactUs,
    meta: {
      id: 18, // Admin Contact Us Page
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
  scrollBehavior(to, from, savedPosition) {
    if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth',
      }
    }
    // always scroll to top
    return { top: 0, behavior: 'smooth' }
  },
})

router.beforeEach((to, from, next) => {
  const token = getCookie('token')
  const userInfo = getCookie('user')

  const userStore = useUserStore()
  userStore.setUser(userInfo)
  const userType = userStore.user?.user_type_name?.trim() || ''

  if (to.meta.public) {
    // If the route is public, allow access
    return next()
  }

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
  const pageInfo = allValidRoutes.find((route) => route.path === path)
  if (pageInfo) {
    visitCount(pageInfo.meta.id.toString())
  } else if (commomRoutes.find((route) => route.path === path)) {
    // if common route, check with role and set id accordingly
    const userStore = useUserStore()
    const userType = userStore.user?.user_type_name?.trim() || ''

    const roleAndPageMap: Record<string, Record<string, number>> = {
      Student: {
        '/articles/:id': 3, // Student Article Detail Page
        '/notifications': 6, // Notification Page
        '/settings': 7, // Setting Page
      },
      'Marketing Coordinator': {
        '/articles/:id': 10, // Marketing Coordinator Article Detail Page
        '/notifications': 6, // Notification Page
        '/settings': 7, // Setting Page
      },
      'Marketing Manager': {
        '/notifications': 6, // Notification Page
        '/settings': 7, // Setting Page
      },
      Admin: {
        '/settings': 7, // Setting Page
      },
      Guest: {
        '/settings': 7, // Setting Page
      },
    }

    const pageId = roleAndPageMap[userType]?.[path] || 0
    if (pageId) {
      visitCount(pageId.toString())
    }
  }
}

export default router
