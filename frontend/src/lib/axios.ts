import axios from 'axios'

const auth = JSON.parse(localStorage.getItem('auth') || '{}')

axios.defaults.baseURL = import.meta.env.VITE_API_URL_API

if (auth.token) {
  axios.defaults.headers.common['Authorization'] = 'Bearer ' + auth.token
}

export default axios
