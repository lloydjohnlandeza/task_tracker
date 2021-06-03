import Vue from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue'
import { InertiaProgress } from '@inertiajs/progress'
import 'vuetify/dist/vuetify.min.css'
import Vuetify from 'vuetify'
Vue.use(Vuetify)

InertiaProgress.init()
createInertiaApp({
  resolve: name => require(`./Pages/${name}`),
  setup({ el, app, props }) {
    new Vue({
      vuetify: new Vuetify(),
      render: h => h(app, props),
    }).$mount(el)
  },
})