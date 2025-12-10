import React from 'react';
import Input from '../ui/Input';

const EducationStep = ({ formData, errors, touched, onChange, onBlur }) => {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-900 bengali-text mb-2">
          শিক্ষাগত যোগ্যতার বিবরণ
        </h2>
        <p className="text-gray-600 bengali-text">
          আপনার শিক্ষাগত যোগ্যতা পূরণ করুন
        </p>
      </div>

      {/* SSC Section */}
      <div className="bg-gray-50 p-6 rounded-lg">
        <h3 className="text-lg font-semibold text-gray-900 bengali-text mb-4">
          SSC / দাখিল / ভোকেশনাল / O levels
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input
            label="Year / সাল"
            name="ssc_year"
            value={formData.ssc_year}
            onChange={(e) => onChange('ssc_year', e.target.value)}
            onBlur={() => onBlur('ssc_year')}
            error={touched.ssc_year && errors.ssc_year}
            placeholder="2020"
            required
          />
          <Input
            label="Board / বোর্ড"
            name="ssc_board"
            value={formData.ssc_board}
            onChange={(e) => onChange('ssc_board', e.target.value)}
            onBlur={() => onBlur('ssc_board')}
            error={touched.ssc_board && errors.ssc_board}
            placeholder="ঢাকা"
            required
            bengali
          />
          <Input
            label="বিভাগ / গ্রুপ / সাবজেক্ট"
            name="ssc_group"
            value={formData.ssc_group}
            onChange={(e) => onChange('ssc_group', e.target.value)}
            onBlur={() => onBlur('ssc_group')}
            error={touched.ssc_group && errors.ssc_group}
            placeholder="বিজ্ঞান"
            required
            bengali
          />
          <Input
            label="Institution / স্কুল / কলেজ"
            name="ssc_institution"
            value={formData.ssc_institution}
            onChange={(e) => onChange('ssc_institution', e.target.value)}
            onBlur={() => onBlur('ssc_institution')}
            error={touched.ssc_institution && errors.ssc_institution}
            placeholder="প্রতিষ্ঠানের নাম"
            required
            bengali
          />
        </div>
      </div>

      {/* HSC Section */}
      <div className="bg-gray-50 p-6 rounded-lg">
        <h3 className="text-lg font-semibold text-gray-900 bengali-text mb-4">
          HSC / আলিম / ডিপ্লোমা / A levels
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input
            label="Year / সাল"
            name="hsc_year"
            value={formData.hsc_year}
            onChange={(e) => onChange('hsc_year', e.target.value)}
            onBlur={() => onBlur('hsc_year')}
            error={touched.hsc_year && errors.hsc_year}
            placeholder="2022"
            required
          />
          <Input
            label="Board / বোর্ড"
            name="hsc_board"
            value={formData.hsc_board}
            onChange={(e) => onChange('hsc_board', e.target.value)}
            onBlur={() => onBlur('hsc_board')}
            error={touched.hsc_board && errors.hsc_board}
            placeholder="ঢাকা"
            required
            bengali
          />
          <Input
            label="বিভাগ / গ্রুপ / সাবজেক্ট"
            name="hsc_group"
            value={formData.hsc_group}
            onChange={(e) => onChange('hsc_group', e.target.value)}
            onBlur={() => onBlur('hsc_group')}
            error={touched.hsc_group && errors.hsc_group}
            placeholder="বিজ্ঞান"
            required
            bengali
          />
          <Input
            label="Institution / কলেজ"
            name="hsc_institution"
            value={formData.hsc_institution}
            onChange={(e) => onChange('hsc_institution', e.target.value)}
            onBlur={() => onBlur('hsc_institution')}
            error={touched.hsc_institution && errors.hsc_institution}
            placeholder="প্রতিষ্ঠানের নাম"
            required
            bengali
          />
        </div>
      </div>

      {/* Graduation Section */}
      <div className="bg-gray-50 p-6 rounded-lg">
        <h3 className="text-lg font-semibold text-gray-900 bengali-text mb-2">
          স্নাতক / মাস্টার্স / সমমানের ডিগ্রি
        </h3>
        <p className="text-sm text-gray-600 bengali-text mb-4">
          কেবল সেরা উচ্চতর ডিগ্রি (ঐচ্ছিক)
        </p>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input
            label="Year / সাল"
            name="graduation_year"
            value={formData.graduation_year}
            onChange={(e) => onChange('graduation_year', e.target.value)}
            onBlur={() => onBlur('graduation_year')}
            error={touched.graduation_year && errors.graduation_year}
            placeholder="2026"
          />
          <Input
            label="University / বিশ্ববিদ্যালয়"
            name="graduation_board"
            value={formData.graduation_board}
            onChange={(e) => onChange('graduation_board', e.target.value)}
            onBlur={() => onBlur('graduation_board')}
            error={touched.graduation_board && errors.graduation_board}
            placeholder="ঢাকা বিশ্ববিদ্যালয়"
            bengali
          />
          <Input
            label="Subject / বিষয়"
            name="graduation_subject"
            value={formData.graduation_subject}
            onChange={(e) => onChange('graduation_subject', e.target.value)}
            onBlur={() => onBlur('graduation_subject')}
            error={touched.graduation_subject && errors.graduation_subject}
            placeholder="কম্পিউটার সায়েন্স"
            bengali
          />
          <Input
            label="Institution / প্রতিষ্ঠান"
            name="graduation_institution"
            value={formData.graduation_institution}
            onChange={(e) => onChange('graduation_institution', e.target.value)}
            onBlur={() => onBlur('graduation_institution')}
            error={touched.graduation_institution && errors.graduation_institution}
            placeholder="প্রতিষ্ঠানের নাম"
            bengali
          />
        </div>
      </div>
    </div>
  );
};

export default EducationStep;
