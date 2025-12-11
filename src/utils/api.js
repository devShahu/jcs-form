import axios from 'axios';

// Use relative URL to go through Vite proxy in development
const API_BASE_URL = import.meta.env.VITE_API_URL || '/api';

// Create axios instance with default config
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  timeout: 120000, // 120 seconds (2 minutes) for PDF generation
  withCredentials: true, // Send cookies with requests
});

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Add any auth tokens here if needed
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response.data;
  },
  (error) => {
    // Handle errors globally
    const message = error.response?.data?.message || error.message || 'একটি ত্রুটি ঘটেছে';
    console.error('API Error:', message);
    return Promise.reject({
      message,
      status: error.response?.status,
      data: error.response?.data,
    });
  }
);

// API methods
export const formAPI = {
  // Get form configuration
  getConfig: () => api.get('/config'),

  // Validate form step
  validateStep: (step, data) => api.post('/validate', { step, data }),

  // Upload photo
  uploadPhoto: (file) => {
    const formData = new FormData();
    formData.append('photo', file);
    return api.post('/upload-photo', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },

  // Submit complete form (with extended timeout for PDF generation)
  submit: (data) => api.post('/submit', data, { timeout: 120000 }),

  // Download PDF
  downloadPDF: (id) => {
    return axios.get(`${API_BASE_URL}/download/${id}`, {
      responseType: 'blob',
    });
  },
};

// Export both named and default
export { api };
export default api;
