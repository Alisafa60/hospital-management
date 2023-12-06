import React, { useState } from 'react';
import axios from 'axios';
import InputField from '../InputFields/InputField.component';

const Login = () => {
  const [formData, setFormData] = useState({
    username: '',
    password: '',
  });

  const [loginStatus, setLoginStatus] = useState(null);

  const handleInputChange = (value, name) => {
    setFormData((prevFormData) => ({
      ...prevFormData,
      [name]: value,
    }));
  };

  const handleLogin = async (e) => {
    e.preventDefault();

    try {
      const response = await axios.post(
        'http://localhost/hospital-management/backend/login.php',
        formData,
        { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }
      );

      if (response.data.status === 'true') {
        console.log('Login successful');
        setLoginStatus('success');
        localStorage.setItem('token', response.data.token);
      } else {
        console.error('Login failed:', response.data.message);
        setLoginStatus('failure');
      }
    } catch (error) {
      console.error('Error during login:', error.message);
      console.log('Error response:', error.response);
      setLoginStatus('failure');
    }
  };

  return (
    <div>
      <h2>Log in</h2>
      <form onSubmit={handleLogin}>
        <InputField label="username" type="text" value={formData.username} onChange={(value) => handleInputChange(value, 'username')} />
        <InputField label="password" type="text" value={formData.password} onChange={(value) => handleInputChange(value, 'password')} />
        <button type="submit">Log in</button>
      </form>
    </div>
  );
};
//eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkI…luIn0.DVg-0bPuSxRX1yj0q-6kfvgERGRRbrVLTg4GqZhV8nQ
export default Login;