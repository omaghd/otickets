import type Department from './Department'
import type Faq from './Faq'

type Category = {
  id: number
  name: string
  slug: string
  faqs_count: number
  tickets_count: number
  canned_responses_count: number
  faqs: Faq[]
  department: Department
  created_at: string
}

export default Category
