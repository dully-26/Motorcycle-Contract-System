import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'https://motorcycle-contract-system-1.onrender.com/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});


api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');

    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);


api.interceptors.response.use(
  (response) => response,

  (error) => {

    const isAuthRoute =
      error.config?.url?.includes('/login') ||
      error.config?.url?.includes('/register');


    if (error.response?.status === 401 && !isAuthRoute) {

      localStorage.removeItem('token');
      localStorage.removeItem('user');

      window.location.href = '/login';
    }


    return Promise.reject(error);
  }
);


export default api;