import type Ticket from './Ticket'

import type User from './User'
import type Attachment from './Attachment'

type Reply = {
  id: number
  content: string
  is_client_reply: boolean
  ticket: Ticket
  user: User
  created_at: string
  attachments: Attachment[]
}

export default Reply
