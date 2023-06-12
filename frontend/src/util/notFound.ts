import router from '@/router'

import type { RouteLocationNormalizedLoaded } from 'vue-router'

const notFound = (route: RouteLocationNormalizedLoaded) => {
  router.push({
    name: 'NotFound',
    params: { pathMatch: route.path.substring(1).split('/') },
    query: route.query,
    hash: route.hash
  })

  return false
}

export default notFound
