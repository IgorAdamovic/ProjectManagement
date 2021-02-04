import Vue from 'vue'
import VueRouter from 'vue-router'
import Projects from '../views/Projects.vue'
import Updates from '../views/Updates.vue'
import Hardware from '../views/Hardware.vue'
import Clients from '../views/Clients.vue'
import Register from '../views/Register.vue'
import Login from '../views/Login.vue'
import AuthGuard from './auth-guard'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Projects',
    component: Projects,
    beforeEnter: AuthGuard,
  },
  {
    path: '/updates',
    name: 'Updates',
    component: Updates,
    beforeEnter: AuthGuard,
  },
  {
    path: '/hardware',
    name: 'Hardware',
    component: Hardware,
    beforeEnter: AuthGuard,
  },
  {
    path: '/clients',
    name: 'Clients',
    component: Clients,
    beforeEnter: AuthGuard,
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    beforeEnter: AuthGuard,
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
]

const router = new VueRouter({
  mode: 'history',
  base:
    process.env.NODE_ENV === 'development'
      ? process.env.BASE_URL
      : 'project_management',
  routes,
})

export default router
