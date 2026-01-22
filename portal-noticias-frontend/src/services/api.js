import axios from 'axios'

const api = axios.create({
    baseURL: 'http://portal-noticias-backend.test',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})

export default api