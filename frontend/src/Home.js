import React, { useState } from "react";
import axios from "axios";
import './styles/index.css';


const Home = ({ onUserCreated }) => {
  const [newUsername, setNewUsername] = useState("");
  const [creationError, setCreationError] = useState("");

  const createUser = async () => {
    try {
      const response = await axios.post(
        `http://localhost:8000/api/user/create/${newUsername}`
      );
      console.log(response.data);

      setNewUsername("");
      setCreationError("");

      onUserCreated();

      alert("User created successfully");
    } catch (error) {
      console.error("Failed to create user", error);
      if (error.response && error.response.data && error.response.data.error) {
        setCreationError(error.response.data.error);
      } else {
        setCreationError("An error occurred while creating the user.");
      }
    }
  };

  return (
     <div className="home-container" style={{ 
      backgroundImage: `url('/digital-hexagons.jpg')`,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat',
      minHeight: '30vh',
      width: '15vw',
      position: 'relative'
  }}>
    <div className="input-group">
        <input
          type="text"
          value={newUsername}
          onChange={(e) => setNewUsername(e.target.value)}
          placeholder="New username"
        />
        <button className="create-button" onClick={createUser}>Create User</button>
      </div>
      {creationError && <p className="error-message">{creationError}</p>}
    </div>
  );
};

export default Home;
