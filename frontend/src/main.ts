import './assets/main.css'

import { createPinia } from 'pinia'
import { configure } from 'vee-validate'
import { createApp } from 'vue'
import { globalCookiesConfig } from 'vue3-cookies'

import App from './App.vue'
import router from './router'

globalCookiesConfig({
  expireTimes: '30d',
  path: '/',
  domain: '',
  secure: false, // need to set this true on deployment
  sameSite: 'None',
})

configure({
  validateOnBlur: false,
})

const app = createApp(App)
const pinia = createPinia()
app.use(pinia)
app.use(router)

app.mount('#app')

export { pinia }
