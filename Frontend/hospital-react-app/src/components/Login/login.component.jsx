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

      console.log('Server Response:', response.data);

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
        <InputField label="username" type="text" value={formData.first_name} onChange={(value) => handleInputChange(value, 'first_name')} />
        <InputField label="passwprd" type="text" value={formData.last_name} onChange={(value) => handleInputChange(value, 'last_name')} />
        <button type="submit">Log in</button>
      </form>
    </div>
  );
};

export default Login;