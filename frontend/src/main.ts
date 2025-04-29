import './assets/main.css'

import { createPinia } from 'pinia'
import { configure } from 'vee-validate'
import { createApp } from 'vue'
import { globalCookiesConfig } from 'vue3-cookies'
import { VueReCaptcha } from 'vue-recaptcha-v3'

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

const recaptchaKey = import.meta.env.VITE_GOOGLE_RECAPTCHA_KEY

const app = createApp(App)
const pinia = createPinia()
app.use(pinia)
app.use(VueReCaptcha, {
  siteKey: recaptchaKey,
  loaderOptions: {
    useRecaptchaNet: true,
    autoHideBadge: false,
  },
})
app.use(router)

app.mount('#app')

export { pinia }
