import React from 'react';

const InputField = ({ label, type, value, onChange }) => (
  <div>
    <label>
      {label}:
      <input
        type={type}
        value={value}
        onChange={(e) => onChange(e.target.value)}
      />
    </label>
    <br />
  </div>
);

export default InputField;