type NewTicket = {
  subject: string
  description: string
  category_id: string
  client_id?: number | null
  agent_id?: number | null
  priority: string
  attachments?: File[]
}

export default NewTicket
