import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import FormWizard from './components/FormWizard';
import Header from './components/Header';
import ErrorBoundary from './components/ErrorBoundary';
import AdminLogin from './pages/Admin/AdminLogin';
import AdminDashboard from './pages/Admin/AdminDashboard';
import SubmissionsList from './pages/Admin/SubmissionsList';
import SubmissionDetail from './pages/Admin/SubmissionDetail';
import Settings from './pages/Admin/Settings';
import './App.css';

// Protected Route Component
function ProtectedRoute({ children }) {
  const token = localStorage.getItem('admin_token');
  
  if (!token) {
    return <Navigate to="/admin/login" replace />;
  }
  
  return children;
}

function App() {
  return (
    <ErrorBoundary>
      <BrowserRouter>
        <Routes>
          {/* Public Form Route */}
          <Route path="/" element={
            <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
              <Header />
              <main className="container mx-auto px-4 py-8">
                <FormWizard />
              </main>
            </div>
          } />

          {/* Admin Routes */}
          <Route path="/admin/login" element={<AdminLogin />} />
          <Route path="/admin/dashboard" element={
            <ProtectedRoute>
              <AdminDashboard />
            </ProtectedRoute>
          } />
          <Route path="/admin/submissions" element={
            <ProtectedRoute>
              <SubmissionsList />
            </ProtectedRoute>
          } />
          <Route path="/admin/submissions/:id" element={
            <ProtectedRoute>
              <SubmissionDetail />
            </ProtectedRoute>
          } />
          <Route path="/admin/settings" element={
            <ProtectedRoute>
              <Settings />
            </ProtectedRoute>
          } />

          {/* Redirect /admin to dashboard */}
          <Route path="/admin" element={<Navigate to="/admin/dashboard" replace />} />
        </Routes>
      </BrowserRouter>
    </ErrorBoundary>
  );
}

export default App;
