import type Department from './Department'

type User = {
  id: number
  name: string
  role: string
  email: string
  phone: string
  picture: string
  department: Department
  created_at: string
  pivot: {
    created_at: string
    transferred_by_user: User
    is_current: boolean
  }
}

export default User
