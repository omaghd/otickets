import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'

import router from './router'

import Toast, { type PluginOptions } from 'vue-toastification'
import 'vue-toastification/dist/index.css'

import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

import { createHead } from 'unhead'

import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

import FloatingVue from 'floating-vue'
import 'floating-vue/dist/style.css'

import Vue3Lottie from 'vue3-lottie'
import 'vue3-lottie/dist/style.css'

import './assets/style.css'

createHead()

const app = createApp(App)

const pinia = createPinia()

pinia.use(piniaPluginPersistedstate)

app.use(pinia)

app.use(router)

const options: PluginOptions = {
  timeout: 4000
}
app.use(Toast, options)

app.component('VueDatePicker', VueDatePicker)

const globalOptions = {
  clipboard: {
    matchVisual: false
  }
}

QuillEditor.props.globalOptions.default = () => globalOptions

app.component('QuillEditor', QuillEditor)

app.use(FloatingVue, {
  themes: {
    'info-tooltip': {
      $extend: 'tooltip',
      $resetCss: true
    }
  }
})

app.use(Vue3Lottie, { name: 'LottieAnimation' })

app.mount('#app')
