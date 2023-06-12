import type User from './User'
import type Category from './Category'
import type Reply from './Reply'
import type Attachment from './Attachment'

type Ticket = {
  id: number
  reference: string
  subject: string
  description: string
  status: string
  priority: string
  replies_count: number
  agents: User[]
  replies: Reply[]
  attachments: Attachment[]
  category: Category
  client: User
  created_at: string
  updated_at: string
}

export default Ticket
