import React, { useState } from 'react';

const Input = ({
  label,
  name,
  type = 'text',
  value,
  onChange,
  onBlur,
  error,
  placeholder,
  required = false,
  disabled = false,
  className = '',
  bengali = false,
  autoCapitalize = false,
  ...props
}) => {
  const [focused, setFocused] = useState(false);
  const inputRef = React.useRef(null);
  
  React.useEffect(() => {
    // Initialize Avro for Bengali inputs using jQuery
    if (bengali && inputRef.current && window.jQuery) {
      try {
        window.jQuery(`#${name}`).avro();
      } catch (e) {
        console.error('Avro init error:', e);
      }
    }
  }, [bengali, name]);
  
  const handleFocus = () => setFocused(true);
  const handleBlur = (e) => {
    setFocused(false);
    if (onBlur) onBlur(e);
  };
  
  const handleChange = (e) => {
    let newValue = e.target.value;
    
    // Auto-capitalize for English fields
    if (autoCapitalize) {
      newValue = newValue.toUpperCase();
    }
    
    // Create a new event with the modified value
    const modifiedEvent = {
      ...e,
      target: {
        ...e.target,
        value: newValue
      }
    };
    
    onChange(modifiedEvent);
  };
  
  const inputClasses = `
    w-full px-4 py-3 rounded-lg border-2 transition-all duration-200
    ${error ? 'border-red-500 focus:border-red-600' : 'border-gray-300 focus:border-red-500'}
    ${disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'}
    focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-20
    ${bengali ? 'bengali-text' : ''}
    ${className}
  `;
  
  return (
    <div className="mb-4">
      {label && (
        <label
          htmlFor={name}
          className={`block text-sm font-medium mb-2 ${bengali ? 'bengali-text' : ''} ${
            error ? 'text-red-600' : 'text-gray-700'
          }`}
        >
          {label}
          {required && <span className="text-red-500 ml-1">*</span>}
        </label>
      )}
      
      <div className="relative">
        <input
          ref={inputRef}
          id={name}
          name={name}
          type={type}
          value={value}
          onChange={handleChange}
          onFocus={handleFocus}
          onBlur={handleBlur}
          placeholder={placeholder}
          disabled={disabled}
          required={required}
          className={inputClasses}
          aria-invalid={error ? 'true' : 'false'}
          aria-describedby={error ? `${name}-error` : undefined}
          {...props}
        />
        
        {value && !error && (
          <div className="absolute right-3 top-1/2 transform -translate-y-1/2">
            <svg className="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
            </svg>
          </div>
        )}
      </div>
      
      {error && (
        <p id={`${name}-error`} className="mt-1 text-sm text-red-600 bengali-text animate-shake">
          {error}
        </p>
      )}
    </div>
  );
};

export default Input;
