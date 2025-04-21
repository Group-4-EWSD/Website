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

const app = createApp(App)
app.use(VueReCaptcha, {
  siteKey: '6LewtB4rAAAAANYyuYZ-c-0hROFl6u0xB8H0xBgu',
  loaderOptions: {
    useRecaptchaNet: true,
    autoHideBadge: false
  },
})
app.use(createPinia())
app.use(router)

app.mount('#app')
