import type Category from './Category'

type Faq = {
  id: number
  question: string
  answer: string
  excerpt: string
  slug: string
  is_published: boolean
  category: Category
  created_at: string
  updated_at: string
}

export default Faq
