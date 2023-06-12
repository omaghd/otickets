import type User from './User'
import type Category from './Category'

type CannedResponse = {
  id: number
  title: string
  content: string
  agent: User
  category: Category
  created_at: string
}

export default CannedResponse
