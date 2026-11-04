import axios from 'axios';

// Use "localhost" in both the browser address and this URL (not 127.0.0.1),
// so the session cookie is shared between React and Laravel.
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8000',
  withCredentials: true,   // send and accept the session cookie
  withXSRFToken: true,     // send the CSRF token Laravel gives us
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

export const getCsrfCookie = () => api.get('/sanctum/csrf-cookie');

export default api;
