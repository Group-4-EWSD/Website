import { createRouter, createWebHistory } from 'vue-router'
const Home = () => import('@/views/Home.vue');
const Login = () => import('@/views/Auth/Login.vue');
const Register = () => import('@/views/Auth/Register.vue');

const publicRoutes = [
  { path: '/', name: 'home', component: Home }
];

const authRoutes = [
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    ...publicRoutes,
    ...authRoutes
  ],
})

export default router
