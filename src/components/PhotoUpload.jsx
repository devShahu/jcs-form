import React, { useState, useRef } from 'react';
import { motion } from 'framer-motion';

const PhotoUpload = ({ value, onChange, error, label = 'পাসপোর্ট সাইজ ছবি', note = '১ কপি, সাদা ব্যাকগ্রাউন্ড (সর্বোচ্চ ৫MB, JPG/PNG)' }) => {
  const [preview, setPreview] = useState(value || null);
  const [isDragging, setIsDragging] = useState(false);
  const fileInputRef = useRef(null);

  const handleFileSelect = (file) => {
    if (!file) return;

    // Validate file type - allow images and PDFs
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    if (!allowedTypes.includes(file.type)) {
      alert('শুধুমাত্র JPG, PNG বা PDF ফাইল আপলোড করুন');
      return;
    }

    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert('ফাইলের সাইজ সর্বোচ্চ ৫MB হতে পারে');
      return;
    }

    // Create preview
    const reader = new FileReader();
    reader.onloadend = () => {
      const base64 = reader.result;
      setPreview(base64);
      onChange(base64);
    };
    reader.readAsDataURL(file);
  };

  const handleDrop = (e) => {
    e.preventDefault();
    setIsDragging(false);
    
    const file = e.dataTransfer.files[0];
    handleFileSelect(file);
  };

  const handleDragOver = (e) => {
    e.preventDefault();
    setIsDragging(true);
  };

  const handleDragLeave = () => {
    setIsDragging(false);
  };

  const handleClick = () => {
    fileInputRef.current?.click();
  };

  const handleFileInputChange = (e) => {
    const file = e.target.files[0];
    handleFileSelect(file);
  };

  const handleRemove = () => {
    setPreview(null);
    onChange('');
    if (fileInputRef.current) {
      fileInputRef.current.value = '';
    }
  };

  return (
    <div className="mb-4">
      <label className="block text-sm font-medium text-gray-700 bengali-text mb-2">
        {label} <span className="text-red-500">*</span>
      </label>
      <p className="text-xs text-gray-500 bengali-text mb-2">
        {note}
      </p>

      {!preview || preview.startsWith('data:application/pdf') ? (
        <motion.div
          onClick={handleClick}
          onDrop={handleDrop}
          onDragOver={handleDragOver}
          onDragLeave={handleDragLeave}
          className={`
            border-2 border-dashed rounded-lg p-8 text-center cursor-pointer
            transition-all duration-200
            ${isDragging ? 'border-red-500 bg-red-50' : 'border-gray-300 hover:border-red-400'}
            ${error ? 'border-red-500' : ''}
          `}
          whileHover={{ scale: 1.01 }}
          whileTap={{ scale: 0.99 }}
        >
          <svg
            className="mx-auto h-12 w-12 text-gray-400"
            stroke="currentColor"
            fill="none"
            viewBox="0 0 48 48"
          >
            <path
              d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
              strokeWidth={2}
              strokeLinecap="round"
              strokeLinejoin="round"
            />
          </svg>
          <p className="mt-2 text-sm text-gray-600 bengali-text">
            {preview && preview.startsWith('data:application/pdf') ? 'PDF আপলোড সম্পন্ন' : 'ফাইল আপলোড করতে ক্লিক করুন বা ড্র্যাগ করুন'}
          </p>
          <p className="mt-1 text-xs text-gray-500">
            JPG, PNG, PDF (max 5MB)
          </p>
        </motion.div>
      ) : (
        <div className="relative inline-block">
          <img
            src={preview}
            alt="Preview"
            className="w-40 h-48 object-cover rounded-lg border-2 border-gray-300 shadow-md"
          />
          <button
            type="button"
            onClick={handleRemove}
            className="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 transition-colors shadow-lg"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      )}

      <input
        ref={fileInputRef}
        type="file"
        accept="image/jpeg,image/png,image/jpg,application/pdf"
        onChange={handleFileInputChange}
        className="hidden"
      />

      {error && (
        <p className="mt-1 text-sm text-red-600 bengali-text animate-shake">
          {error}
        </p>
      )}
    </div>
  );
};

export default PhotoUpload;
