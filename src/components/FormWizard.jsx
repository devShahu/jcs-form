import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import PersonalInfoStep from './steps/PersonalInfoStep';
import EducationStep from './steps/EducationStep';
import DeclarationStep from './steps/DeclarationStep';
import ProgressBar from './ProgressBar';
import Button from './ui/Button';
import SuccessModal from './SuccessModal';
import { getInitialFormState, saveFormDraft, loadFormDraft, clearFormDraft, debounce } from '../utils/formState';
import { formAPI } from '../utils/api';

const steps = [
  { id: 1, name: 'ব্যক্তিগত তথ্য', nameEn: 'Personal Info', component: PersonalInfoStep },
  { id: 2, name: 'শিক্ষাগত যোগ্যতা', nameEn: 'Education', component: EducationStep },
  { id: 3, name: 'ঘোষণা', nameEn: 'Declaration', component: DeclarationStep },
];

const FormWizard = () => {
  const [currentStep, setCurrentStep] = useState(1);
  const [formData, setFormData] = useState(getInitialFormState());
  const [errors, setErrors] = useState({});
  const [touched, setTouched] = useState({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [showSuccess, setShowSuccess] = useState(false);
  const [submissionId, setSubmissionId] = useState(null);

  // Load draft on mount
  useEffect(() => {
    const draft = loadFormDraft();
    if (draft) {
      setFormData(draft);
    }
  }, []);

  // Auto-save draft
  const autoSave = debounce((data) => {
    saveFormDraft(data);
  }, 1000);

  useEffect(() => {
    autoSave(formData);
  }, [formData]);

  const handleFieldChange = (name, value) => {
    setFormData(prev => ({ ...prev, [name]: value }));
    setTouched(prev => ({ ...prev, [name]: true }));
    
    // Clear error for this field
    if (errors[name]) {
      setErrors(prev => {
        const newErrors = { ...prev };
        delete newErrors[name];
        return newErrors;
      });
    }
  };

  const handleFieldBlur = (name) => {
    setTouched(prev => ({ ...prev, [name]: true }));
  };

  const validateCurrentStep = async () => {
    // Use client-side validation only for now
    return validateClientSide();
    
    /* API validation disabled temporarily
    const stepMap = {
      1: 'personal',
      2: 'education',
      3: 'declaration',
    };

    try {
      const response = await formAPI.validateStep(stepMap[currentStep], formData);
      console.log('Validation response:', response);
      
      if (response.success) {
        setErrors({});
        return true;
      } else {
        setErrors(response.errors || {});
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return false;
      }
    } catch (error) {
      console.error('Validation error:', error);
      return validateClientSide();
    }
    */
  };

  const validateClientSide = () => {
    // Simple client-side validation as fallback
    const requiredFields = {
      1: ['name_bangla', 'name_english', 'father_name', 'mother_name', 'photo', 'mobile_number', 'nid_birth_reg', 'birth_date', 'present_address', 'permanent_address', 'document_type', 'document_file'],
      2: ['ssc_year', 'ssc_board', 'ssc_group', 'ssc_institution', 'hsc_year', 'hsc_board', 'hsc_group', 'hsc_institution'],
      3: ['movement_role', 'aspirations', 'declaration_name', 'declaration_agreement'],
    };

    const fields = requiredFields[currentStep] || [];
    const newErrors = {};

    fields.forEach(field => {
      const value = formData[field];
      
      // Special handling for different field types
      if (field === 'declaration_agreement') {
        if (value !== true) {
          newErrors[field] = 'আপনাকে এই শর্তে সম্মত হতে হবে';
        }
      } else if (field === 'photo') {
        // Check if photo is uploaded (base64 string or file path)
        if (!value || value === '') {
          newErrors[field] = 'ছবি আপলোড করুন';
        }
      } else {
        // Regular text fields
        if (!value || value === '') {
          newErrors[field] = 'এই ফিল্ডটি আবশ্যক';
        }
      }
    });

    console.log('Client-side validation errors:', newErrors);
    setErrors(newErrors);
    
    if (Object.keys(newErrors).length > 0) {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    return Object.keys(newErrors).length === 0;
  };

  const handleNext = async () => {
    console.log('Current form data:', formData);
    console.log('Current step:', currentStep);
    
    const isValid = await validateCurrentStep();
    console.log('Is valid:', isValid);
    console.log('Errors:', errors);
    
    if (isValid && currentStep < steps.length) {
      setCurrentStep(prev => prev + 1);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
      console.log('Validation failed, staying on current step');
    }
  };

  const handleBack = () => {
    if (currentStep > 1) {
      setCurrentStep(prev => prev - 1);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  };

  const handleSubmit = async () => {
    setIsSubmitting(true);
    
    try {
      const response = await formAPI.submit(formData);
      
      if (response.success) {
        setSubmissionId(response.submission_id);
        setShowSuccess(true);
        clearFormDraft();
      } else {
        setErrors(response.errors || {});
        alert('ফরম জমা দিতে সমস্যা হয়েছে। অনুগ্রহ করে ত্রুটিগুলি সংশোধন করুন।');
      }
    } catch (error) {
      console.error('Submission error:', error);
      alert('ফরম জমা দিতে সমস্যা হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleNewForm = () => {
    setShowSuccess(false);
    setSubmissionId(null);
    setFormData(getInitialFormState());
    setErrors({});
    setTouched({});
    setCurrentStep(1);
    clearFormDraft();
  };

  const CurrentStepComponent = steps[currentStep - 1].component;

  return (
    <>
      <div className="max-w-4xl mx-auto">
        <ProgressBar currentStep={currentStep} steps={steps} />
        
        <div className="bg-white rounded-xl shadow-lg p-8 mt-8">
          <AnimatePresence mode="wait">
            <motion.div
              key={currentStep}
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, x: -20 }}
              transition={{ duration: 0.3 }}
            >
              <CurrentStepComponent
                formData={formData}
                errors={errors}
                touched={touched}
                onChange={handleFieldChange}
                onBlur={handleFieldBlur}
              />
            </motion.div>
          </AnimatePresence>

          <div className="flex justify-between mt-8 pt-6 border-t border-gray-200">
            <Button
              variant="secondary"
              onClick={handleBack}
              disabled={currentStep === 1 || isSubmitting}
            >
              ← পূর্ববর্তী
            </Button>

            {currentStep < steps.length ? (
              <Button onClick={handleNext} disabled={isSubmitting}>
                পরবর্তী →
              </Button>
            ) : (
              <Button onClick={handleSubmit} loading={isSubmitting}>
                ফরম জমা দিন
              </Button>
            )}
          </div>
        </div>
      </div>

      {showSuccess && (
        <SuccessModal
          submissionId={submissionId}
          onClose={handleNewForm}
        />
      )}
    </>
  );
};

export default FormWizard;
