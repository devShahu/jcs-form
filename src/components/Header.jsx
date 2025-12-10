import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

const Header = () => {
  const [orgName, setOrgName] = useState('জাতীয় ছাত্রশক্তি');
  const [logoPath, setLogoPath] = useState(null);

  useEffect(() => {
    loadSettings();
  }, []);

  const loadSettings = async () => {
    try {
      const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';
      const response = await axios.get(`${API_BASE_URL}/settings`);
      if (response.data.success) {
        setOrgName(response.data.data.org_name_bn || 'জাতীয় ছাত্রশক্তি');
        setLogoPath(response.data.data.logo_path);
      }
    } catch (err) {
      // Use defaults if settings can't be loaded
      console.log('Using default settings');
    }
  };

  return (
    <header className="bg-white shadow-sm border-b border-gray-200">
      <div className="container mx-auto px-4 py-6">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-4">
            {logoPath ? (
              <div className="w-16 h-16 rounded-full overflow-hidden shadow-lg bg-white flex items-center justify-center">
                <img 
                  src={logoPath} 
                  alt="Logo" 
                  className="w-full h-full object-contain"
                  onError={(e) => {
                    e.target.style.display = 'none';
                    e.target.parentElement.innerHTML = '<div class="w-16 h-16 bg-gradient-to-br from-red-600 to-red-700 rounded-full flex items-center justify-center"><span class="text-white font-bold text-2xl">JCS</span></div>';
                  }}
                />
              </div>
            ) : (
              <div className="w-16 h-16 bg-gradient-to-br from-red-600 to-red-700 rounded-full flex items-center justify-center shadow-lg">
                <span className="text-white font-bold text-2xl">JCS</span>
              </div>
            )}
            <div>
              <h1 className="text-2xl font-bold text-gray-900 bengali-text">
                {orgName}
              </h1>
              <p className="text-sm text-gray-600 bengali-text">সদস্য ফরম</p>
            </div>
          </div>
          <div className="flex items-center gap-4">
            <div className="hidden md:block">
              <p className="text-sm text-gray-500">Membership Form</p>
            </div>
            <Link 
              to="/admin" 
              className="text-sm text-gray-600 hover:text-red-600 transition-colors flex items-center gap-1"
              title="Admin Panel"
            >
              <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
              </svg>
              <span className="hidden sm:inline">Admin</span>
            </Link>
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header;
