type TicketsFilters = {
  query?: string | null
  trash?: boolean | null
  client?: string | null
  agent?: string | null
  status?: string | null
  priority?: string | null
  category?: number | null
  dates?: string[] | null
}

export default TicketsFilters
