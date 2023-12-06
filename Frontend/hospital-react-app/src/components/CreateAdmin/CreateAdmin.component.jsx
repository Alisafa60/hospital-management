import React, { useState } from 'react';
import axios from 'axios';
import InputField from '../InputFields/InputField.component';

const CreateAdmin = () => {
  const [formData, setFormData] = useState({
    admin_username: '',
    admin_password: '',
    first_name: '',
    last_name: '',
  });


  const handleInputChange = (value, name) => {
    setFormData((prevFormData) => ({
      ...prevFormData,
      [name]: value,
    }));
  };

  const handleSignup = async (e) => {
    e.preventDefault();

    try {
      const response = await axios.post(
        'http://localhost/hospital-management/backend/create_admin.php',
        formData,
        { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
      );      
      if (response.data.status === 'true') {
        console.log('Signup successful');
        localStorage.setItem('token', response.data.token);
      } else {
        console.error('Signup failed:', response.data.message);
      }
    } catch (error) {
      console.error('Error during signup:', error.message);
    }
  };

  return (
    <div>
      <h2>Signup</h2>
      <form onSubmit={handleSignup}>
        <InputField label="Username" type="text" value={formData.admin_username} onChange={(value) => handleInputChange(value, 'admin_username')} />
        <InputField label="Password" type="password" value={formData.admin_password} onChange={(value) => handleInputChange(value, 'admin_password')} />
        <InputField label="First Name" type="text" value={formData.first_name} onChange={(value) => handleInputChange(value, 'first_name')} />
        <InputField label="Last Name" type="text" value={formData.last_name} onChange={(value) => handleInputChange(value, 'last_name')} />
        <button type="submit">Signup</button>
      </form>
    </div>
  );
};

export default CreateAdmin;