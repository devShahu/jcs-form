// Form state management utilities

const STORAGE_KEY = 'jcs_form_draft';

// Save form state to localStorage
export const saveFormDraft = (formData) => {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
  } catch (error) {
    console.error('Error saving form draft:', error);
  }
};

// Load form state from localStorage
export const loadFormDraft = () => {
  try {
    const draft = localStorage.getItem(STORAGE_KEY);
    return draft ? JSON.parse(draft) : null;
  } catch (error) {
    console.error('Error loading form draft:', error);
    return null;
  }
};

// Clear form draft
export const clearFormDraft = () => {
  try {
    localStorage.removeItem(STORAGE_KEY);
  } catch (error) {
    console.error('Error clearing form draft:', error);
  }
};

// Initial form state
export const getInitialFormState = () => ({
  // Personal Info
  form_no: '',
  name_bangla: '',
  name_english: '',
  father_name: '',
  mother_name: '',
  photo: '',
  mobile_number: '',
  blood_group: '',
  nid_birth_reg: '',
  birth_date: '',
  present_address: '',
  permanent_address: '',
  political_affiliation: '',
  last_position: '',
  document_type: '',
  document_file: '',
  
  // Education
  ssc_year: '',
  ssc_board: '',
  ssc_group: '',
  ssc_institution: '',
  hsc_year: '',
  hsc_board: '',
  hsc_group: '',
  hsc_institution: '',
  graduation_year: '',
  graduation_board: '',
  graduation_subject: '',
  graduation_institution: '',
  
  // Declaration
  movement_role: '',
  aspirations: '',
  declaration_name: '',
  declaration_agreement: false,
  committee_member_name: '',
  committee_member_position: '',
  committee_member_comments: '',
  recommended_position: '',
});

// Debounce function for auto-save
export const debounce = (func, wait) => {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
};
