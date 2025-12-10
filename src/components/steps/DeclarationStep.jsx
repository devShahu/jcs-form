import React from 'react';
import Input from '../ui/Input';
import TextArea from '../ui/TextArea';

const DeclarationStep = ({ formData, errors, touched, onChange, onBlur }) => {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-900 bengali-text mb-2">
          ঘোষণা
        </h2>
        <p className="text-gray-600 bengali-text">
          আপনার ঘোষণা এবং সম্মতি প্রদান করুন
        </p>
      </div>

      <TextArea
        label="জুলাই–আগস্ট ২০২৪ এ ছাত্র–জনতার গণঅভ্যুত্থানে আপনার ভূমিকা ও সংযুক্তি"
        name="movement_role"
        value={formData.movement_role}
        onChange={(e) => onChange('movement_role', e.target.value)}
        onBlur={() => onBlur('movement_role')}
        error={touched.movement_role && errors.movement_role}
        placeholder="আপনার ভূমিকা লিখুন"
        rows={4}
        required
        bengali
        maxLength={1000}
      />

      <TextArea
        label="জাতীয় ছাত্রশক্তির জন্য আপনার চাওয়া এবং আকাঙ্ক্ষা কি?"
        name="aspirations"
        value={formData.aspirations}
        onChange={(e) => onChange('aspirations', e.target.value)}
        onBlur={() => onBlur('aspirations')}
        error={touched.aspirations && errors.aspirations}
        placeholder="আপনার চাওয়া এবং আকাঙ্ক্ষা লিখুন"
        rows={6}
        required
        bengali
        maxLength={1000}
      />

      <div className="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <h3 className="font-semibold text-blue-900 bengali-text mb-2">অঙ্গীকারনামা</h3>
        <p className="text-sm text-blue-800 bengali-text leading-relaxed">
          আমি এই মর্মে ঘোষণা করছি যে, এই ফরমে প্রদত্ত সমস্ত তথ্য সত্য এবং সঠিক। আমি জাতীয় ছাত্রশক্তির আদর্শ ও নীতিমালা মেনে চলতে এবং সংগঠনের প্রতি দায়বদ্ধ থাকতে সম্মত।
        </p>
      </div>

      <Input
        label="আমি"
        name="declaration_name"
        value={formData.declaration_name}
        onChange={(e) => onChange('declaration_name', e.target.value)}
        onBlur={() => onBlur('declaration_name')}
        error={touched.declaration_name && errors.declaration_name}
        placeholder="আপনার নাম"
        required
        bengali
      />

      <div className="flex items-start">
        <input
          type="checkbox"
          id="declaration_agreement"
          name="declaration_agreement"
          checked={formData.declaration_agreement}
          onChange={(e) => onChange('declaration_agreement', e.target.checked)}
          className="mt-1 h-5 w-5 text-red-600 border-gray-300 rounded focus:ring-red-500"
        />
        <label
          htmlFor="declaration_agreement"
          className="ml-3 text-sm text-gray-700 bengali-text"
        >
          আমি ঘোষণা করছি যে উপরের সমস্ত তথ্য সত্য এবং সঠিক{' '}
          <span className="text-red-500">*</span>
        </label>
      </div>
      {touched.declaration_agreement && errors.declaration_agreement && (
        <p className="text-sm text-red-600 bengali-text animate-shake">
          {errors.declaration_agreement}
        </p>
      )}

      <div className="border-t border-gray-200 pt-6 mt-6">
        <h3 className="text-lg font-semibold text-gray-900 bengali-text mb-4">
          সাট কমিটি কর্তৃক পূরণীয় (ঐচ্ছিক)
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input
            label="সাট কমিটির সদস্যের নাম"
            name="committee_member_name"
            value={formData.committee_member_name}
            onChange={(e) => onChange('committee_member_name', e.target.value)}
            onBlur={() => onBlur('committee_member_name')}
            error={touched.committee_member_name && errors.committee_member_name}
            placeholder="কমিটি সদস্যের নাম"
            bengali
          />
          <Input
            label="পদবী"
            name="committee_member_position"
            value={formData.committee_member_position}
            onChange={(e) => onChange('committee_member_position', e.target.value)}
            onBlur={() => onBlur('committee_member_position')}
            error={touched.committee_member_position && errors.committee_member_position}
            placeholder="পদবী"
            bengali
          />
          <div className="md:col-span-2">
            <TextArea
              label="সাট কমিটির সদস্যের মন্তব্য"
              name="committee_member_comments"
              value={formData.committee_member_comments}
              onChange={(e) => onChange('committee_member_comments', e.target.value)}
              onBlur={() => onBlur('committee_member_comments')}
              error={touched.committee_member_comments && errors.committee_member_comments}
              placeholder="মন্তব্য"
              rows={3}
              bengali
              maxLength={500}
            />
          </div>
          <Input
            label="রেকমেন্ডকৃত পদবী"
            name="recommended_position"
            value={formData.recommended_position}
            onChange={(e) => onChange('recommended_position', e.target.value)}
            onBlur={() => onBlur('recommended_position')}
            error={touched.recommended_position && errors.recommended_position}
            placeholder="রেকমেন্ডকৃত পদবী"
            bengali
          />
        </div>
      </div>
    </div>
  );
};

export default DeclarationStep;
