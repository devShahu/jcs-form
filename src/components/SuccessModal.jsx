import React from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import Button from './ui/Button';
import { formAPI } from '../utils/api';

const SuccessModal = ({ submissionId, onClose }) => {
  const handleDownload = async () => {
    try {
      const response = await formAPI.downloadPDF(submissionId);
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `jcs_membership_${submissionId}.pdf`);
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (error) {
      console.error('Download error:', error);
      alert('PDF ডাউনলোড করতে সমস্যা হয়েছে');
    }
  };

  return (
    <AnimatePresence>
      <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
        <motion.div
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          exit={{ opacity: 0, scale: 0.9 }}
          className="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8"
        >
          <div className="text-center">
            {/* Success Animation */}
            <motion.div
              initial={{ scale: 0 }}
              animate={{ scale: 1 }}
              transition={{ delay: 0.2, type: 'spring', stiffness: 200 }}
              className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6"
            >
              <svg className="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
              </svg>
            </motion.div>

            <h2 className="text-3xl font-bold text-gray-900 bengali-text mb-2">
              সফলভাবে জমা হয়েছে!
            </h2>
            <p className="text-gray-600 bengali-text mb-6">
              আপনার ফরম সফলভাবে জমা হয়েছে। এখন আপনি PDF ডাউনলোড করতে পারেন।
            </p>

            <div className="bg-gray-50 rounded-lg p-4 mb-6">
              <p className="text-sm text-gray-600 bengali-text mb-1">
                সাবমিশন আইডি
              </p>
              <p className="text-lg font-mono font-semibold text-gray-900">
                {submissionId}
              </p>
            </div>

            <div className="space-y-3">
              <Button
                onClick={handleDownload}
                className="w-full"
                size="lg"
              >
                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                PDF ডাউনলোড করুন
              </Button>

              <Button
                onClick={onClose}
                variant="outline"
                className="w-full"
                size="lg"
              >
                নতুন ফরম পূরণ করুন
              </Button>
            </div>

            <p className="text-xs text-gray-500 bengali-text mt-6">
              আপনার PDF সংরক্ষিত হয়েছে। পরে ডাউনলোড করতে সাবমিশন আইডি ব্যবহার করুন।
            </p>
          </div>
        </motion.div>
      </div>
    </AnimatePresence>
  );
};

export default SuccessModal;
