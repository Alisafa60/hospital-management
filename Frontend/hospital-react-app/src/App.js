import logo from './logo.svg';
import './App.css';
import axios from 'axios';
import Signup from './components/Signup/Signup.component';
import SignupForm from './components/Signup/Signup.component';
import CreateAdmin from './components/CreateAdmin/CreateAdmin.component';
import Login from './components/Login/login.component';
import AdminApproval from './components/ApproveUser/ApproveUser.component';

function App() {
  return (
    <div>
      <AdminApproval />
    </div>
  );
}

export default App;
