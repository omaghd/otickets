import { createRouter, createWebHistory } from 'vue-router'

import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(''),
  routes: [
    {
      path: '/auth',
      component: () => import('@/layout/AuthLayout.vue'),
      meta: { forGuest: true },
      children: [
        {
          path: 'login',
          name: 'Login',
          component: () => import('@/views/Auth/Login.vue')
        },
        {
          path: 'register',
          name: 'Register',
          component: () => import('@/views/Auth/Register.vue')
        },
        {
          path: 'forgot-password',
          name: 'ForgotPassword',
          component: () => import('@/views/Auth/ForgotPassword.vue')
        },
        {
          path: 'reset-password',
          name: 'ResetPassword',
          component: () => import('@/views/Auth/ResetPassword.vue'),
          beforeEnter: (to, from, next) => {
            if (!to.query.token || !to.query.email) next({ name: 'Login' })

            next()
          }
        }
      ]
    },
    {
      path: '/',
      component: () => import('@/layout/HomeLayout.vue'),
      children: [
        {
          path: '',
          name: 'Home',
          component: () => import('@/views/Home/Index.vue')
        },
        {
          path: 'faqs/:slug',
          name: 'SingleFaq',
          component: () => import('@/views/Home/Faqs/SingleFaq.vue')
        },
        {
          path: 'category/:slug',
          name: 'HomeCategory',
          component: () => import('@/views/Home/Category.vue')
        },
        {
          path: ':pathMatch(.*)*',
          name: 'NotFound',
          component: () => import('@/views/Home/NotFound.vue')
        }
      ]
    },
    {
      path: '/client',
      component: () => import('@/layout/ClientLayout.vue'),
      meta: { forClient: true },
      children: [
        {
          path: 'tickets',
          name: 'ClientTickets',
          component: () => import('@/views/Client/Tickets.vue')
        },
        {
          path: 'tickets/create',
          name: 'ClientNewTicket',
          component: () => import('@/views/Client/NewTicket.vue')
        },
        {
          path: 'tickets/:reference',
          name: 'ClientSingleTicket',
          component: () => import('@/views/Client/SingleTicket.vue')
        },
        {
          path: 'profile',
          name: 'ClientProfile',
          component: () => import('@/views/Client/Profile.vue')
        },
        {
          path: 'password',
          name: 'ClientPassword',
          component: () => import('@/views/Client/Password.vue')
        },
        {
          path: 'notifications',
          name: 'ClientNotifications',
          component: () => import('@/views/Client/Notifications.vue')
        }
      ]
    },
    {
      path: '/dashboard',
      component: () => import('@/layout/ManagerLayout.vue'),
      meta: { forManager: true },
      children: [
        {
          path: '',
          name: 'Dashboard',
          component: () => import('@/views/Manager/Dashboard.vue')
        },
        {
          path: 'users',
          name: 'DashboardUsers',
          component: () => import('@/views/Admin/Users.vue'),
          meta: { forAdmin: true }
        },
        {
          path: 'departments',
          name: 'DashboardDepartments',
          component: () => import('@/views/Admin/Departments.vue'),
          meta: { forAdmin: true }
        },
        {
          path: 'clients',
          name: 'DashboardClients',
          component: () => import('@/views/Admin/Clients.vue'),
          meta: { forAdmin: true }
        },
        {
          path: 'categories',
          name: 'DashboardCategories',
          component: () => import('@/views/Admin/Categories.vue'),
          meta: { forAdmin: true }
        },
        {
          path: 'faqs',
          name: 'DashboardFaqs',
          component: () => import('@/views/Admin/Faqs.vue'),
          meta: { forAdmin: true }
        },
        {
          path: 'newsletters',
          name: 'DashboardNewsletters',
          component: () => import('@/views/Admin/Newsletters.vue'),
          meta: { forAdmin: true }
        },
        {
          path: 'canned-responses',
          name: 'DashboardCannedResponses',
          component: () => import('@/views/Manager/CannedResponses.vue')
        },
        {
          path: 'tickets/:reference',
          name: 'DashboardSingleTicket',
          component: () => import('@/views/Manager/SingleTicket.vue')
        },
        {
          path: 'tickets',
          name: 'DashboardTickets',
          component: () => import('@/views/Manager/Tickets.vue')
        },
        {
          path: 'tickets/:reference',
          name: 'DashboardSingleTicket',
          component: () => import('@/views/Manager/SingleTicket.vue')
        },
        {
          path: 'profile',
          name: 'DashboardProfile',
          component: () => import('@/views/Manager/Profile.vue')
        },
        {
          path: 'password',
          name: 'DashboardPassword',
          component: () => import('@/views/Manager/Password.vue')
        },
        {
          path: 'notifications',
          name: 'DashboardNotifications',
          component: () => import('@/views/Manager/Notifications.vue')
        },
        {
          path: ':pathMatch(.*)*',
          name: 'DashboardNotFound',
          component: () => import('@/views/Manager/NotFound.vue')
        }
      ]
    }
  ],
  scrollBehavior(to) {
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth' }
    }

    return { top: 0, behavior: 'smooth' }
  }
})

export default router

router.beforeEach((to, from, next) => {
  const { user, isClient, isManager, isAdmin } = storeToRefs(useAuthStore())

  if (to.meta.forClient && !isClient.value) next({ name: 'Login' })
  else if (to.meta.forManager && !isManager.value) next({ name: 'Login' })
  else if (to.meta.forAdmin && !isAdmin.value) next({ name: 'Login' })
  else if (to.meta.forGuest && user.value.token) next({ name: 'Home' })

  next()
})
