type Notification = {
  id: string
  data: {
    reference?: string
    message: string
  }
  read_at: string | null
  created_at: string
  updated_at: string
}

export default Notification
