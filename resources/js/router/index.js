import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('../pages/LoginPage.vue'),
    meta: { public: true }
  },
  // Traveler routes
  {
    path: '/traveler',
    redirect: '/traveler/search',
    meta: { requiresAuth: true, role: 'traveler' }
  },
  {
    path: '/traveler/search',
    name: 'traveler.search',
    component: () => import('../pages/traveler/SearchPage.vue'),
    meta: { requiresAuth: true, role: 'traveler' }
  },
  {
    path: '/traveler/wallet',
    name: 'traveler.wallet',
    component: () => import('../pages/traveler/WalletPage.vue'),
    meta: { requiresAuth: true, role: 'traveler' }
  },
  {
    path: '/traveler/history',
    redirect: '/traveler/wallet',
  },
  // Approver routes
  {
    path: '/approver',
    redirect: '/approver/dashboard',
    meta: { requiresAuth: true, role: 'approver' }
  },
  {
    path: '/approver/dashboard',
    name: 'approver.dashboard',
    component: () => import('../pages/approver/DashboardPage.vue'),
    meta: { requiresAuth: true, role: 'approver' }
  },
  { path: '/approver/ranking', redirect: '/approver/dashboard' },
  // Catch-all redirect
  {
    path: '/:pathMatch(.*)*',
    redirect: '/login'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  // Restore session on page reload
  if (!auth.isAuthenticated && !auth.checked) {
    await auth.fetchUser()
  }

  // Public route - allow
  if (to.meta.public) {
    // If already authenticated, redirect to their area
    if (auth.isAuthenticated) {
      return auth.role === 'traveler' ? '/traveler/search' : '/approver/dashboard'
    }
    return true
  }

  // Requires auth
  if (to.meta.requiresAuth) {
    if (!auth.isAuthenticated) {
      return '/login'
    }
    // Role check
    if (to.meta.role && auth.role !== to.meta.role) {
      return auth.role === 'traveler' ? '/traveler/search' : '/approver/dashboard'
    }
  }

  return true
})

export default router
