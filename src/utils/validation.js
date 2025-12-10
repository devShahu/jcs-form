import { z } from 'zod';

// Personal Info Schema
export const personalInfoSchema = z.object({
  form_no: z.string().max(50).optional(),
  name_bangla: z.string().min(2, 'নাম কমপক্ষে ২ অক্ষর হতে হবে').max(100),
  name_english: z.string().min(2, 'Name must be at least 2 characters').max(100),
  father_name: z.string().min(2, 'পিতার নাম কমপক্ষে ২ অক্ষর হতে হবে').max(100),
  mother_name: z.string().min(2, 'মাতার নাম কমপক্ষে ২ অক্ষর হতে হবে').max(100),
  photo: z.string().min(1, 'ছবি আবশ্যক'),
  mobile_number: z.string().regex(/^01[3-9]\d{8}$/, 'সঠিক মোবাইল নম্বর দিন (01XXXXXXXXX)'),
  nid_birth_reg: z.string().min(10, 'NID/জন্ম নিবন্ধন নম্বর কমপক্ষে ১০ অক্ষর হতে হবে').max(20),
  birth_date: z.string().regex(/^\d{4}-\d{2}-\d{2}$/, 'সঠিক তারিখ দিন'),
  present_address: z.string().min(10, 'ঠিকানা কমপক্ষে ১০ অক্ষর হতে হবে').max(500),
  permanent_address: z.string().min(10, 'ঠিকানা কমপক্ষে ১০ অক্ষর হতে হবে').max(500),
  political_affiliation: z.string().max(200).optional(),
  last_position: z.string().max(200).optional(),
});

// Education Schema
export const educationSchema = z.object({
  ssc_year: z.string().regex(/^\d{4}$/, 'সঠিক বছর দিন'),
  ssc_board: z.string().min(2).max(100),
  ssc_group: z.string().min(2).max(100),
  ssc_institution: z.string().min(2).max(200),
  
  hsc_year: z.string().regex(/^\d{4}$/, 'সঠিক বছর দিন'),
  hsc_board: z.string().min(2).max(100),
  hsc_group: z.string().min(2).max(100),
  hsc_institution: z.string().min(2).max(200),
  
  graduation_year: z.string().regex(/^\d{4}$/, 'সঠিক বছর দিন').optional().or(z.literal('')),
  graduation_board: z.string().max(100).optional(),
  graduation_subject: z.string().max(100).optional(),
  graduation_institution: z.string().max(200).optional(),
});

// Declaration Schema
export const declarationSchema = z.object({
  responsibility_ideology: z.string().min(50, 'কমপক্ষে ৫০ অক্ষর লিখুন').max(1000),
  declaration_name: z.string().min(2).max(100),
  declaration_agreement: z.boolean().refine(val => val === true, {
    message: 'আপনাকে এই শর্তে সম্মত হতে হবে',
  }),
  committee_member_name: z.string().max(100).optional(),
  committee_member_position: z.string().max(100).optional(),
  committee_member_comments: z.string().max(500).optional(),
  recommended_position: z.string().max(100).optional(),
});

// Complete form schema
export const completeFormSchema = personalInfoSchema
  .merge(educationSchema)
  .merge(declarationSchema);

// Validate a single field
export const validateField = (schema, fieldName, value) => {
  try {
    const fieldSchema = schema.shape[fieldName];
    if (fieldSchema) {
      fieldSchema.parse(value);
      return { valid: true, error: null };
    }
    return { valid: true, error: null };
  } catch (error) {
    return {
      valid: false,
      error: error.errors[0]?.message || 'Invalid value',
    };
  }
};

// Validate entire form or section
export const validateForm = (schema, data) => {
  try {
    schema.parse(data);
    return { valid: true, errors: {} };
  } catch (error) {
    const errors = {};
    error.errors.forEach((err) => {
      const field = err.path[0];
      errors[field] = err.message;
    });
    return { valid: false, errors };
  }
};
