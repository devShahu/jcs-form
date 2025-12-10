import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { adminAPI } from '../../utils/adminApi';
import Button from '../../components/ui/Button';

export default function SubmissionDetail() {
  const [submission, setSubmission] = useState(null);
  const [loading, setLoading] = useState(true);
  const { id } = useParams();
  const navigate = useNavigate();

  useEffect(() => {
    loadSubmission();
  }, [id]);

  const loadSubmission = async () => {
    try {
      const response = await adminAPI.getSubmission(id);
      
      if (response.success) {
        setSubmission(response.data);
      }
    } catch (err) {
      console.error('Error loading submission:', err);
      if (err.status === 401) {
        navigate('/admin/login');
      }
    } finally {
      setLoading(false);
    }
  };

  const downloadPDF = async () => {
    try {
      const response = await adminAPI.downloadPDF(id);
      
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `jcs_membership_${id}.pdf`);
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (err) {
      console.error('Error downloading PDF:', err);
      alert('Failed to download PDF');
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-100 flex items-center justify-center">
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
          <p className="mt-2 text-gray-600">Loading submission...</p>
        </div>
      </div>
    );
  }

  if (!submission) {
    return (
      <div className="min-h-screen bg-gray-100 flex items-center justify-center">
        <div className="text-center">
          <p className="text-gray-600">Submission not found</p>
          <Button onClick={() => navigate('/admin/submissions')} className="mt-4">
            Back to Submissions
          </Button>
        </div>
      </div>
    );
  }

  const data = submission.data || {};

  return (
    <div className="min-h-screen bg-gray-100">
      {/* Header */}
      <div className="bg-white shadow">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex justify-between items-center">
            <h1 className="text-2xl font-bold text-gray-900">Submission Details</h1>
            <div className="flex gap-2">
              <Button onClick={downloadPDF} size="sm">
                Download PDF
              </Button>
              <Button onClick={() => navigate('/admin/submissions')} variant="secondary" size="sm">
                Back to List
              </Button>
            </div>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="bg-white rounded-lg shadow overflow-hidden">
          {/* Submission Info */}
          <div className="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <p className="text-sm font-medium text-gray-500">Submission ID</p>
                <p className="mt-1 text-sm text-gray-900">{submission.submission_id}</p>
              </div>
              <div>
                <p className="text-sm font-medium text-gray-500">Submitted At</p>
                <p className="mt-1 text-sm text-gray-900">
                  {new Date(submission.submitted_at).toLocaleString()}
                </p>
              </div>
              <div>
                <p className="text-sm font-medium text-gray-500">IP Address</p>
                <p className="mt-1 text-sm text-gray-900">{submission.ip_address}</p>
              </div>
            </div>
          </div>

          {/* Personal Information */}
          <div className="px-6 py-4 border-b border-gray-200">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <InfoField label="Name (Bengali)" value={data.name_bangla} />
              <InfoField label="Name (English)" value={data.name_english} />
              <InfoField label="Father's Name" value={data.father_name} />
              <InfoField label="Mother's Name" value={data.mother_name} />
              <InfoField label="Mobile Number" value={data.mobile_number} />
              <InfoField label="Blood Group" value={data.blood_group} />
              <InfoField label="NID/Birth Registration" value={data.nid_birth_reg} />
              <InfoField label="Birth Date" value={data.birth_date} />
            </div>
          </div>

          {/* Address */}
          <div className="px-6 py-4 border-b border-gray-200">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">Address</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <InfoField label="Present Address" value={data.present_address} />
              <InfoField label="Permanent Address" value={data.permanent_address} />
            </div>
          </div>

          {/* Background */}
          <div className="px-6 py-4 border-b border-gray-200">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">Background</h2>
            <div className="space-y-4">
              <InfoField label="Political/Organizational Affiliation" value={data.political_affiliation} />
              <InfoField label="Last Position" value={data.last_position} />
            </div>
          </div>

          {/* Education */}
          <div className="px-6 py-4 border-b border-gray-200">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">Educational Qualification</h2>
            <div className="space-y-4">
              <div>
                <h3 className="font-medium text-gray-700 mb-2">SSC/Dakhil</h3>
                <div className="grid grid-cols-2 md:grid-cols-4 gap-2">
                  <InfoField label="Year" value={data.ssc_year} />
                  <InfoField label="Board" value={data.ssc_board} />
                  <InfoField label="Group" value={data.ssc_group} />
                  <InfoField label="Institution" value={data.ssc_institution} />
                </div>
              </div>
              <div>
                <h3 className="font-medium text-gray-700 mb-2">HSC/Alim</h3>
                <div className="grid grid-cols-2 md:grid-cols-4 gap-2">
                  <InfoField label="Year" value={data.hsc_year} />
                  <InfoField label="Board" value={data.hsc_board} />
                  <InfoField label="Group" value={data.hsc_group} />
                  <InfoField label="Institution" value={data.hsc_institution} />
                </div>
              </div>
              <div>
                <h3 className="font-medium text-gray-700 mb-2">Graduation</h3>
                <div className="grid grid-cols-2 md:grid-cols-4 gap-2">
                  <InfoField label="Year" value={data.graduation_year} />
                  <InfoField label="University" value={data.graduation_board} />
                  <InfoField label="Subject" value={data.graduation_subject} />
                  <InfoField label="Institution" value={data.graduation_institution} />
                </div>
              </div>
            </div>
          </div>

          {/* Movement & Aspirations */}
          <div className="px-6 py-4">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">Movement & Aspirations</h2>
            <div className="space-y-4">
              <InfoField label="Movement Role (July-August 2024)" value={data.movement_role} />
              <InfoField label="Aspirations for JCS" value={data.aspirations} />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

function InfoField({ label, value }) {
  return (
    <div>
      <p className="text-sm font-medium text-gray-500">{label}</p>
      <p className="mt-1 text-sm text-gray-900">{value || 'N/A'}</p>
    </div>
  );
}
