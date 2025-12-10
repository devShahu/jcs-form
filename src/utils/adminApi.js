import axios from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

// Create axios instance for admin API with credentials
const adminApi = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  withCredentials: true, // Important: send cookies with requests
  timeout: 120000, // 120 seconds for PDF generation
});

// Response interceptor
adminApi.interceptors.response.use(
  (response) => {
    return response.data;
  },
  (error) => {
    const message = error.response?.data?.message || error.message || 'An error occurred';
    console.error('Admin API Error:', message);
    
    // Handle 401 Unauthorized
    if (error.response?.status === 401) {
      // Redirect to login if not already there
      if (!window.location.pathname.includes('/admin/login')) {
        window.location.href = '/admin/login';
      }
    }
    
    return Promise.reject({
      message,
      status: error.response?.status,
      data: error.response?.data,
    });
  }
);

// Admin API methods
export const adminAPI = {
  // Authentication
  login: (username, password) => 
    adminApi.post('/admin/login', { username, password }),
  
  logout: () => 
    adminApi.post('/admin/logout'),

  // Submissions
  getSubmissions: (page = 1, limit = 10) => 
    adminApi.get('/admin/submissions', { params: { page, limit } }),
  
  getSubmission: (id) => 
    adminApi.get(`/admin/submissions/${id}`),
  
  searchSubmissions: (query, dateFrom = null, dateTo = null, page = 1, limit = 10) => 
    adminApi.get('/admin/search', { 
      params: { query, date_from: dateFrom, date_to: dateTo, page, limit } 
    }),

  // PDF Operations
  downloadPDF: (id) => {
    return axios.get(`${API_BASE_URL}/admin/submissions/${id}/pdf`, {
      responseType: 'blob',
      withCredentials: true,
    });
  },

  testPDF: () => {
    // Add timestamp to prevent caching
    const timestamp = new Date().getTime();
    return axios.get(`${API_BASE_URL}/admin/test-pdf?t=${timestamp}`, {
      responseType: 'blob',
      withCredentials: true,
      timeout: 120000, // 2 minutes for PDF generation
    });
  },

  testHTML: () => {
    // Add timestamp to prevent caching
    const timestamp = new Date().getTime();
    return axios.get(`${API_BASE_URL}/admin/test-html?t=${timestamp}`, {
      responseType: 'blob',
      withCredentials: true,
    });
  },

  // Settings
  getSettings: () => 
    adminApi.get('/admin/settings', { timeout: 120000 }),
  
  updateSettings: (settings) => 
    adminApi.post('/admin/settings', settings),
  
  uploadLogo: (file) => {
    const formData = new FormData();
    formData.append('logo', file);
    return axios.post(`${API_BASE_URL}/admin/upload-logo`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      withCredentials: true,
    });
  },
};

export default adminApi;
