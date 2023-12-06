import React, { useState } from 'react';
import axios from 'axios';
import InputField from '../InputFields/InputField.component';

const AdminApproval = () => {
  const [userDetails, setUserDetails] = useState({
    user_id: '',
    new_role: '',
  });
  const [approvalStatus, setApprovalStatus] = useState(null);

  const handleInputChange = (value, name) => {
    setUserDetails((prevUserDetails) => ({
      ...prevUserDetails,
      [name]: value,
    }));
  };

  const handleApproval = async (e) => {
    e.preventDefault();

    try {
      const token = localStorage.getItem('token');

      const response = await axios.post(
        'http://localhost/hospital-management/backend/admin_approve.php',
        userDetails,
        {
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            Authorization: `Bearer ${token}`,
          },
        }
      );

      console.log('Server Response:', response.data);

      if (response.data.status === 'true') {
        setApprovalStatus({ success: true, message: response.data.message });
      } else {
        setApprovalStatus({ success: false, message: response.data.message });
      }
    } catch (error) {
      console.error('Error during approval:', error.message);
      console.log('Error response:', error.response);
      setApprovalStatus({ success: false, message: 'Error during approval' });
    }
  };

  return (
    <div>
      <h2>Admin Approval</h2>
      <form onSubmit={handleApproval}>
        <InputField label="User ID" type="text" value={userDetails.user_id} onChange={(value) => handleInputChange(value, 'user_id')} />
        <InputField label="New Role" type="text" value={userDetails.new_role} onChange={(value) => handleInputChange(value, 'new_role')} />
        <button type="submit">Approve User</button>
      </form>

      {approvalStatus && (
        <p>
          {approvalStatus.success
            ? 'User approved successfully: ' + approvalStatus.message
            : 'Approval failed: ' + approvalStatus.message}
        </p>
      )}
    </div>
  );
};

export default AdminApproval;
