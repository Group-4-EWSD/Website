import './assets/main.css'

import { addIcons, OhVueIcon } from 'oh-vue-icons'
import * as FaIcons from 'oh-vue-icons/icons/fa'
import { createPinia } from 'pinia'
import { configure } from 'vee-validate'
import { createApp } from 'vue'
import { globalCookiesConfig } from 'vue3-cookies'

import App from './App.vue'
import router from './router'


const Fa = Object.values({ ...FaIcons })
addIcons(...Fa)

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
app.component('v-icon', OhVueIcon)
app.use(createPinia())
app.use(router)

app.mount('#app')
