import React from 'react';
import Input from '../ui/Input';
import TextArea from '../ui/TextArea';
import PhotoUpload from '../PhotoUpload';

const PersonalInfoStep = ({ formData, errors, touched, onChange, onBlur }) => {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-900 bengali-text mb-2">
          ব্যক্তিগত তথ্য
        </h2>
        <p className="text-gray-600 bengali-text">
          আপনার ব্যক্তিগত তথ্য সঠিকভাবে পূরণ করুন
        </p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="md:col-span-2">
          <Input
            label="ফরম নং (সেট কমিটি কর্তৃক পূরণীয়)"
            name="form_no"
            value={formData.form_no}
            onChange={(e) => onChange('form_no', e.target.value)}
            onBlur={() => onBlur('form_no')}
            error={touched.form_no && errors.form_no}
            placeholder="সেট কমিটি কর্তৃক পূরণীয়"
            bengali
          />
        </div>

        <Input
          label="নাম (বাংলায়)"
          name="name_bangla"
          value={formData.name_bangla}
          onChange={(e) => onChange('name_bangla', e.target.value)}
          onBlur={() => onBlur('name_bangla')}
          error={touched.name_bangla && errors.name_bangla}
          placeholder="আপনার নাম বাংলায় লিখুন"
          required
          bengali
        />

        <Input
          label="নাম (ইংরেজি বড় হাত)"
          name="name_english"
          value={formData.name_english}
          onChange={(e) => onChange('name_english', e.target.value)}
          onBlur={() => onBlur('name_english')}
          error={touched.name_english && errors.name_english}
          placeholder="YOUR NAME IN CAPITAL LETTERS"
          required
          autoCapitalize
        />

        <Input
          label="পিতার নাম (বাংলায়)"
          name="father_name"
          value={formData.father_name}
          onChange={(e) => onChange('father_name', e.target.value)}
          onBlur={() => onBlur('father_name')}
          error={touched.father_name && errors.father_name}
          placeholder="পিতার নাম বাংলায় লিখুন"
          required
          bengali
        />

        <Input
          label="মাতার নাম (বাংলায়)"
          name="mother_name"
          value={formData.mother_name}
          onChange={(e) => onChange('mother_name', e.target.value)}
          onBlur={() => onBlur('mother_name')}
          error={touched.mother_name && errors.mother_name}
          placeholder="মাতার নাম বাংলায় লিখুন"
          required
          bengali
        />

        <div className="md:col-span-2">
          <PhotoUpload
            value={formData.photo}
            onChange={(value) => onChange('photo', value)}
            error={touched.photo && errors.photo}
          />
        </div>

        <div className="md:col-span-2 mt-6">
          <h3 className="text-lg font-semibold text-gray-900 bengali-text mb-4">
            সংযুক্তি (জাতীয় পরিচয়পত্র / জন্ম নিবন্ধন / শিক্ষার্থী পরিচয়পত্র)
          </h3>
          
          <div className="mb-4">
            <label htmlFor="document_type" className="block text-sm font-medium text-gray-700 bengali-text mb-2">
              ডকুমেন্ট টাইপ <span className="text-red-500">*</span>
            </label>
            <select
              id="document_type"
              name="document_type"
              value={formData.document_type}
              onChange={(e) => onChange('document_type', e.target.value)}
              onBlur={() => onBlur('document_type')}
              className="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-20 transition-all duration-200 bengali-text"
            >
              <option value="">নির্বাচন করুন</option>
              <option value="nid">জাতীয় পরিচয়পত্র (NID)</option>
              <option value="birth_certificate">জন্ম নিবন্ধন</option>
              <option value="student_id">শিক্ষার্থী পরিচয়পত্র</option>
            </select>
            {touched.document_type && errors.document_type && (
              <p className="mt-1 text-sm text-red-600 bengali-text">{errors.document_type}</p>
            )}
          </div>

          <PhotoUpload
            value={formData.document_file}
            onChange={(value) => onChange('document_file', value)}
            error={touched.document_file && errors.document_file}
            label="ডকুমেন্ট আপলোড করুন"
            note="JPG/PNG/PDF (সর্বোচ্চ 5MB)"
          />
        </div>

        <Input
          label="মোবাইল নাম্বার"
          name="mobile_number"
          type="tel"
          value={formData.mobile_number}
          onChange={(e) => onChange('mobile_number', e.target.value)}
          onBlur={() => onBlur('mobile_number')}
          error={touched.mobile_number && errors.mobile_number}
          placeholder="01XXXXXXXXX"
          required
          bengali
        />

        <div className="mb-4">
          <label htmlFor="blood_group" className="block text-sm font-medium text-gray-700 bengali-text mb-2">
            রক্তের গ্রুপ
          </label>
          <select
            id="blood_group"
            name="blood_group"
            value={formData.blood_group}
            onChange={(e) => onChange('blood_group', e.target.value)}
            onBlur={() => onBlur('blood_group')}
            className="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-20 transition-all duration-200"
          >
            <option value="">নির্বাচন করুন</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
          </select>
        </div>

        <Input
          label="এনআইডি / জন্ম নিবন্ধন নম্বর"
          name="nid_birth_reg"
          value={formData.nid_birth_reg}
          onChange={(e) => onChange('nid_birth_reg', e.target.value)}
          onBlur={() => onBlur('nid_birth_reg')}
          error={touched.nid_birth_reg && errors.nid_birth_reg}
          placeholder="NID বা জন্ম নিবন্ধন নম্বর"
          required
          bengali
        />

        <Input
          label="জন্ম তারিখ (দিন-মাস-সাল)"
          name="birth_date"
          type="date"
          value={formData.birth_date}
          onChange={(e) => onChange('birth_date', e.target.value)}
          onBlur={() => onBlur('birth_date')}
          error={touched.birth_date && errors.birth_date}
          required
          bengali
        />

        <div className="md:col-span-2">
          <TextArea
            label="বর্তমান ঠিকানা"
            name="present_address"
            value={formData.present_address}
            onChange={(e) => onChange('present_address', e.target.value)}
            onBlur={() => onBlur('present_address')}
            error={touched.present_address && errors.present_address}
            placeholder="বর্তমান ঠিকানা লিখুন"
            rows={3}
            required
            bengali
            maxLength={500}
          />
        </div>

        <div className="md:col-span-2">
          <TextArea
            label="স্থায়ী ঠিকানা"
            name="permanent_address"
            value={formData.permanent_address}
            onChange={(e) => onChange('permanent_address', e.target.value)}
            onBlur={() => onBlur('permanent_address')}
            error={touched.permanent_address && errors.permanent_address}
            placeholder="স্থায়ী ঠিকানা লিখুন"
            rows={3}
            required
            bengali
            maxLength={500}
          />
        </div>

        <Input
          label="রাজনৈতিক / সাংগঠনিক সম্পৃক্ততা"
          name="political_affiliation"
          value={formData.political_affiliation}
          onChange={(e) => onChange('political_affiliation', e.target.value)}
          onBlur={() => onBlur('political_affiliation')}
          error={touched.political_affiliation && errors.political_affiliation}
          placeholder="যদি থাকে"
          bengali
        />

        <Input
          label="সর্বশেষ পদবী (কমিটির নাম সহ; যদি থাকে)"
          name="last_position"
          value={formData.last_position}
          onChange={(e) => onChange('last_position', e.target.value)}
          onBlur={() => onBlur('last_position')}
          error={touched.last_position && errors.last_position}
          placeholder="যদি থাকে"
          bengali
        />
      </div>
    </div>
  );
};

export default PersonalInfoStep;
