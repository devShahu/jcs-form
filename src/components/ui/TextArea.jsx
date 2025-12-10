import React, { useState } from 'react';

const TextArea = ({
  label,
  name,
  value,
  onChange,
  onBlur,
  error,
  placeholder,
  required = false,
  disabled = false,
  rows = 4,
  maxLength,
  className = '',
  bengali = false,
  ...props
}) => {
  const [focused, setFocused] = useState(false);
  const textareaRef = React.useRef(null);
  
  React.useEffect(() => {
    // Initialize Avro for Bengali textareas using jQuery
    if (bengali && textareaRef.current && window.jQuery) {
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
  
  const textareaClasses = `
    w-full px-4 py-3 rounded-lg border-2 transition-all duration-200 resize-none
    ${error ? 'border-red-500 focus:border-red-600' : 'border-gray-300 focus:border-red-500'}
    ${disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'}
    focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-20
    ${bengali ? 'bengali-text' : ''}
    ${className}
  `;
  
  const charCount = value?.length || 0;
  const showCount = maxLength && charCount > 0;
  
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
      
      <textarea
        ref={textareaRef}
        id={name}
        name={name}
        value={value}
        onChange={onChange}
        onFocus={handleFocus}
        onBlur={handleBlur}
        placeholder={placeholder}
        disabled={disabled}
        required={required}
        rows={rows}
        maxLength={maxLength}
        className={textareaClasses}
        aria-invalid={error ? 'true' : 'false'}
        aria-describedby={error ? `${name}-error` : undefined}
        {...props}
      />
      
      <div className="flex justify-between items-center mt-1">
        <div>
          {error && (
            <p id={`${name}-error`} className="text-sm text-red-600 bengali-text animate-shake">
              {error}
            </p>
          )}
        </div>
        {showCount && (
          <p className="text-sm text-gray-500">
            {charCount} / {maxLength}
          </p>
        )}
      </div>
    </div>
  );
};

export default TextArea;
