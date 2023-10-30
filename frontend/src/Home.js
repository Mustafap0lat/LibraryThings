import React, { useState } from "react";
import axios from "axios";
import './styles/index.css';


const Home = ({ onUserCreated }) => {
  const [newUsername, setNewUsername] = useState("");
  const [creationError, setCreationError] = useState(""); // To display the error returned from server

  const createUser = async () => {
    try {
      // Assuming that the username is passed directly in the URL, and not as a request body or query parameter.
      const response = await axios.post(
        `http://localhost:8000/api/user/create/${newUsername}`
      );

      // Handle the response according to your needs
      console.log(response.data); // New user data

      // Reset input field and error state
      setNewUsername("");
      setCreationError("");

      // Here we call the onUserCreated function passed in as a prop to notify the parent component.
      onUserCreated(); // <--- This line is crucial

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
