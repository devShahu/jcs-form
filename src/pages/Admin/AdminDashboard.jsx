import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { adminAPI } from '../../utils/adminApi';
import Button from '../../components/ui/Button';

export default function AdminDashboard() {
  const [stats, setStats] = useState({ total: 0, recent: 0 });
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();
  const username = localStorage.getItem('admin_username');

  useEffect(() => {
    loadStats();
  }, []);

  const loadStats = async () => {
    try {
      const response = await adminAPI.getSubmissions(1, 10);
      
      if (response.success) {
        setStats({
          total: response.pagination.total,
          recent: response.data.length
        });
      }
    } catch (err) {
      console.error('Error loading stats:', err);
      if (err.status === 401) {
        navigate('/admin/login');
      }
    } finally {
      setLoading(false);
    }
  };

  const handleLogout = async () => {
    try {
      await adminAPI.logout();
    } catch (err) {
      console.error('Logout error:', err);
    } finally {
      localStorage.removeItem('admin_token');
      localStorage.removeItem('admin_username');
      navigate('/admin/login');
    }
  };

  return (
    <div className="min-h-screen bg-gray-100">
      {/* Header */}
      <div className="bg-white shadow">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex justify-between items-center">
            <h1 className="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
            <div className="flex items-center gap-4">
              <span className="text-sm text-gray-600">Welcome, {username}</span>
              <Button onClick={handleLogout} variant="secondary" size="sm">
                Logout
              </Button>
            </div>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
            <p className="mt-2 text-gray-600">Loading...</p>
          </div>
        ) : (
          <>
            {/* Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div className="bg-white rounded-lg shadow p-6">
                <h3 className="text-lg font-semibold text-gray-700 mb-2">Total Submissions</h3>
                <p className="text-4xl font-bold text-blue-600">{stats.total}</p>
              </div>
              
              <div className="bg-white rounded-lg shadow p-6">
                <h3 className="text-lg font-semibold text-gray-700 mb-2">Recent Submissions</h3>
                <p className="text-4xl font-bold text-green-600">{stats.recent}</p>
              </div>
            </div>

            {/* Quick Actions */}
            <div className="bg-white rounded-lg shadow p-6">
              <h2 className="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Button
                  onClick={() => navigate('/admin/submissions')}
                  className="w-full"
                >
                  View All Submissions
                </Button>
                
                <Button
                  onClick={() => navigate('/admin/submissions?search=true')}
                  variant="secondary"
                  className="w-full"
                >
                  Search Submissions
                </Button>

                <Button
                  onClick={() => navigate('/admin/settings')}
                  variant="secondary"
                  className="w-full"
                >
                  Settings & Test PDF
                </Button>
              </div>
            </div>
          </>
        )}
      </div>
    </div>
  );
}
