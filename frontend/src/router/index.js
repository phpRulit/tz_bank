import Vue from 'vue'
import VueRouter from 'vue-router'
import Form from '../views/Form.vue'
import NewRules from '../views/NewRules.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'form',
    component: Form
  },
  {
    path: '/new-rules',
    name: 'new.rules',
    component: NewRules
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
