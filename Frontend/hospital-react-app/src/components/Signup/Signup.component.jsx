
import React, { useState } from 'react';
import axios from 'axios';
import InputField from '../InputFields/InputField.component';


const Signup = () => {
  const [formData, setFormData] = useState({
    first_name: '',
    last_name: '',
    email: '',
    username: '',
    password: '',
  });

  const handleInputChange = (value, name) => {
    setFormData((prevFormData) => ({
      ...prevFormData,
      [name]: value,
    }));
  };

  const handleSignup = async (e) => {
    e.preventDefault();
    console.log('formdata', formData);

    try {
      const response = await axios.post(
        'http://localhost/hospital-management/backend/signup.php',
        formData,
        { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
      );      
      console.log('Server Response:', response.data);

      if (response.data.status === 'true') {
        console.log('Signup successful');
        localStorage.setItem('token', response.data.token);
      } else {
        console.error('Signup failed:', response.data.message);
      }
    } catch (error) {
      console.error('Error during signup:', error.message);
      console.log('Error response:', error.response);
    }
  };
  
  return (
    <div>
      <h2>Signup</h2>
      <form onSubmit={handleSignup}>
        <InputField label="First Name" type="text" value={formData.first_name} onChange={(value) => handleInputChange(value, 'first_name')} />
        <InputField label="Last Name" type="text" value={formData.last_name} onChange={(value) => handleInputChange(value, 'last_name')} />
        <InputField label="Email" type="email" value={formData.email} onChange={(value) => handleInputChange(value, 'email')} />
        <InputField label="Username" type="text" value={formData.username} onChange={(value) => handleInputChange(value, 'username')} />
        <InputField label="Password" type="password" value={formData.password} onChange={(value) => handleInputChange(value, 'password')} />
        <button type="submit">Signup</button>
      </form>
    </div>
  );
};

export default Signup;