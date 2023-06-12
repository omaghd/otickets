import type Faq from './Faq'

type PaginatedFaqs = {
  current_page: number
  data: Faq[]
  first_page_url: string
  from: number
  last_page: number
  last_page_url: string
  links: {
    url: string
    label: string
    active: boolean
  }[]
  next_page_url: string
  per_page: number
  prev_page_url: string
  to: number
  total: number
}

export default PaginatedFaqs
