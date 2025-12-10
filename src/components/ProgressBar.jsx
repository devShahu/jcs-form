import React from 'react';
import { motion } from 'framer-motion';

const ProgressBar = ({ currentStep, steps }) => {
  return (
    <div className="w-full">
      <div className="flex items-center justify-between mb-2">
        {steps.map((step, index) => (
          <div key={step.id} className="flex-1 flex items-center">
            <div className="flex flex-col items-center flex-1">
              <motion.div
                className={`
                  w-10 h-10 rounded-full flex items-center justify-center font-semibold
                  transition-all duration-300
                  ${
                    currentStep > step.id
                      ? 'bg-green-500 text-white'
                      : currentStep === step.id
                      ? 'bg-red-600 text-white ring-4 ring-red-200'
                      : 'bg-gray-200 text-gray-500'
                  }
                `}
                initial={{ scale: 0.8 }}
                animate={{ scale: currentStep === step.id ? 1.1 : 1 }}
                transition={{ duration: 0.3 }}
              >
                {currentStep > step.id ? (
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                  </svg>
                ) : (
                  step.id
                )}
              </motion.div>
              <div className="mt-2 text-center">
                <p className={`
                  text-sm font-medium bengali-text
                  ${currentStep >= step.id ? 'text-gray-900' : 'text-gray-500'}
                `}>
                  {step.name}
                </p>
                <p className="text-xs text-gray-500 hidden md:block">
                  {step.nameEn}
                </p>
              </div>
            </div>
            
            {index < steps.length - 1 && (
              <div className="flex-1 h-1 mx-4 relative" style={{ marginTop: '-40px' }}>
                <div className="absolute inset-0 bg-gray-200 rounded"></div>
                <motion.div
                  className="absolute inset-0 bg-red-600 rounded"
                  initial={{ width: '0%' }}
                  animate={{ width: currentStep > step.id ? '100%' : '0%' }}
                  transition={{ duration: 0.5 }}
                ></motion.div>
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
};

export default ProgressBar;
