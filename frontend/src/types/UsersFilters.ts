type UsersFilters = {
  page?: number | null
  trash?: boolean | null
  query?: string | null
  department?: number | null
  role?: 'admin' | 'agent' | null
  dates?: string[] | null
  paginate?: boolean | null
}

export default UsersFilters
