import Vue from 'vue'
import Router from 'vue-router'
import RoomPage from '@/components/RoomPage'
import LoginPage from '@/components/LoginPage'
import MainPage from '@/components/MainPage'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/login',
      name: 'LoginPage',
      component: LoginPage
    },
    {
      path: '/',
      name: 'MainPage',
      component: MainPage
    },
    {
      path: '/room',
      name: 'RoomPage',
      component: RoomPage
    }
  ]
})
