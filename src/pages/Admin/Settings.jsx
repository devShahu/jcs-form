import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import { adminAPI } from '../../utils/adminApi';

export default function Settings() {
  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [message, setMessage] = useState('');
  const [orgName, setOrgName] = useState('জাতীয় ছাত্রশক্তি');
  const [orgNameEn, setOrgNameEn] = useState('Jatiya Chhatra Shakti');
  const [logoPath, setLogoPath] = useState('/images/logo.png');
  const [logoPreview, setLogoPreview] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    loadSettings();
  }, []);

  const loadSettings = async () => {
    try {
      const response = await adminAPI.getSettings();
      if (response.success) {
        setOrgName(response.data.org_name_bn || 'জাতীয় ছাত্রশক্তি');
        setOrgNameEn(response.data.org_name_en || 'Jatiya Chhatra Shakti');
        setLogoPath(response.data.logo_path || '/images/logo.png');
      }
    } catch (err) {
      console.error('Error loading settings:', err);
    }
  };

  const handleTestPDF = async () => {
    setLoading(true);
    setMessage('');
    
    try {
      const response = await adminAPI.testPDF();
      
      // Create blob URL and open in new tab
      const blob = new Blob([response.data], { type: 'application/pdf' });
      const url = window.URL.createObjectURL(blob);
      window.open(url, '_blank');
      
      setMessage('Test PDF generated successfully! Check the new tab.');
    } catch (err) {
      console.error('Test PDF error:', err);
      setMessage('Error: ' + (err.message || 'Failed to generate PDF'));
    } finally {
      setLoading(false);
    }
  };

  const handleTestHTML = async () => {
    setLoading(true);
    setMessage('');
    
    try {
      const response = await adminAPI.testHTML();
      
      // Create blob URL and open in new tab
      const blob = new Blob([response.data], { type: 'text/html' });
      const url = window.URL.createObjectURL(blob);
      window.open(url, '_blank');
      
      setMessage('Test HTML generated successfully! Check the new tab.');
    } catch (err) {
      console.error('Test HTML error:', err);
      setMessage('Error: ' + (err.message || 'Failed to generate HTML'));
    } finally {
      setLoading(false);
    }
  };

  const handleSaveSettings = async () => {
    setSaving(true);
    setMessage('');
    
    try {
      const response = await adminAPI.updateSettings({
        org_name_bn: orgName,
        org_name_en: orgNameEn
      });
      
      if (response.success) {
        setMessage('Settings saved successfully!');
      } else {
        setMessage('Error: Failed to save settings');
      }
    } catch (err) {
      console.error('Error saving settings:', err);
      setMessage('Error: ' + (err.message || 'Failed to save settings'));
    } finally {
      setSaving(false);
    }
  };

  const handleLogoChange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // Validate file type
    if (!file.type.startsWith('image/')) {
      setMessage('Error: Please select an image file');
      return;
    }

    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
      setMessage('Error: File size must be less than 2MB');
      return;
    }

    // Show preview
    const reader = new FileReader();
    reader.onloadend = () => {
      setLogoPreview(reader.result);
    };
    reader.readAsDataURL(file);

    // Upload
    setUploading(true);
    setMessage('');
    
    try {
      const response = await adminAPI.uploadLogo(file);
      
      if (response.data.success) {
        setLogoPath(response.data.path);
        setMessage('Logo uploaded successfully!');
        // Reload settings to get updated logo path
        await loadSettings();
      } else {
        setMessage('Error: ' + (response.data.message || 'Failed to upload logo'));
        setLogoPreview(null);
      }
    } catch (err) {
      console.error('Error uploading logo:', err);
      setMessage('Error: ' + (err.message || 'Failed to upload logo'));
      setLogoPreview(null);
    } finally {
      setUploading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-100">
      {/* Header */}
      <div className="bg-white shadow">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex justify-between items-center">
            <h1 className="text-2xl font-bold text-gray-900">Settings</h1>
            <Button onClick={() => navigate('/admin/dashboard')} variant="secondary" size="sm">
              Back to Dashboard
            </Button>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {message && (
          <div className={`mb-6 p-4 rounded ${
            message.includes('Error') ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'
          }`}>
            {message}
          </div>
        )}

        {/* Organization Settings */}
        <div className="bg-white rounded-lg shadow p-6 mb-6">
          <h2 className="text-xl font-bold text-gray-900 mb-4">Organization Settings</h2>
          
          <div className="space-y-4">
            <Input
              label="Organization Name (Bengali)"
              value={orgName}
              onChange={(e) => setOrgName(e.target.value)}
              className="bengali-text"
            />
            
            <Input
              label="Organization Name (English)"
              value={orgNameEn}
              onChange={(e) => setOrgNameEn(e.target.value)}
            />

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Organization Logo
              </label>
              <div className="flex items-center gap-4">
                <div className="w-24 h-24 border-2 border-gray-300 rounded flex items-center justify-center bg-gray-50 overflow-hidden">
                  {logoPreview ? (
                    <img 
                      src={logoPreview} 
                      alt="Logo Preview" 
                      className="max-w-full max-h-full object-contain"
                    />
                  ) : (
                    <img 
                      src={logoPath} 
                      alt="Logo" 
                      className="max-w-full max-h-full object-contain"
                      onError={(e) => {
                        e.target.style.display = 'none';
                        e.target.parentElement.innerHTML += '<span class="text-gray-400 text-xs">No Logo</span>';
                      }}
                    />
                  )}
                </div>
                <div>
                  <input
                    type="file"
                    accept="image/*"
                    className="hidden"
                    id="logo-upload"
                    onChange={handleLogoChange}
                    disabled={uploading}
                  />
                  <label 
                    htmlFor="logo-upload"
                    className={`inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 ${
                      uploading 
                        ? 'bg-gray-300 text-gray-500 cursor-not-allowed' 
                        : 'bg-white text-gray-700 border-2 border-gray-300 hover:border-gray-400 focus:ring-gray-500 cursor-pointer'
                    }`}
                  >
                    {uploading ? 'Uploading...' : 'Upload New Logo'}
                  </label>
                  <p className="text-xs text-gray-500 mt-1">
                    Max 2MB, PNG or JPG
                  </p>
                </div>
              </div>
            </div>

            <Button onClick={handleSaveSettings} className="mt-4" disabled={saving}>
              {saving ? 'Saving...' : 'Save Settings'}
            </Button>
          </div>
        </div>

        {/* PDF Testing */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-bold text-gray-900 mb-4">PDF Testing</h2>
          
          <p className="text-gray-600 mb-4">
            Generate a test PDF with demo data to verify that PDF generation is working correctly.
            The PDF will open in a new tab.
          </p>

          <div className="bg-blue-50 border border-blue-200 rounded p-4 mb-4">
            <h3 className="font-semibold text-blue-900 mb-2">Test PDF will include:</h3>
            <ul className="text-sm text-blue-800 space-y-1">
              <li>• Demo personal information (Bengali and English)</li>
              <li>• Sample educational qualifications</li>
              <li>• Movement role and aspirations</li>
              <li>• Complete two-page layout</li>
              <li>• Proper Bengali font rendering</li>
            </ul>
          </div>

          <div className="flex gap-4">
            <Button 
              onClick={handleTestPDF} 
              disabled={loading}
              className="w-full sm:w-auto"
            >
              {loading ? 'Generating PDF...' : 'Generate Test PDF'}
            </Button>
            
            <Button 
              onClick={handleTestHTML} 
              disabled={loading}
              variant="secondary"
              className="w-full sm:w-auto"
            >
              {loading ? 'Generating...' : 'Generate Test HTML'}
            </Button>
          </div>

          <div className="mt-4 p-4 bg-gray-50 rounded">
            <h4 className="font-semibold text-gray-900 mb-2">Troubleshooting:</h4>
            <ul className="text-sm text-gray-600 space-y-1">
              <li>• If PDF doesn't open, check browser popup blocker</li>
              <li>• If PDF is corrupted, check PHP error logs</li>
              <li>• Ensure TCPDF library is installed: <code className="bg-gray-200 px-1">composer install</code></li>
              <li>• Check that templates/pdf_template.html exists</li>
            </ul>
          </div>
        </div>

        {/* Admin Credentials */}
        <div className="bg-white rounded-lg shadow p-6 mt-6">
          <h2 className="text-xl font-bold text-gray-900 mb-4">Admin Credentials</h2>
          
          <div className="bg-yellow-50 border border-yellow-200 rounded p-4 mb-4">
            <p className="text-sm text-yellow-800">
              <strong>Current credentials:</strong><br />
              Username: admin<br />
              Password: admin123<br />
              <br />
              To change the password, edit <code className="bg-yellow-100 px-1">config/admin.php</code>
            </p>
          </div>

          <p className="text-sm text-gray-600">
            Generate a new password hash:
          </p>
          <pre className="bg-gray-100 p-2 rounded text-xs mt-2 overflow-x-auto">
php -r "echo password_hash('your_new_password', PASSWORD_DEFAULT);"
          </pre>
        </div>
      </div>
    </div>
  );
}
