import React, { useState } from "react";
import axios from "axios";

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
    <div>
      <input
        type="text"
        value={newUsername}
        onChange={(e) => setNewUsername(e.target.value)}
        placeholder="New username"
      />
      <button onClick={createUser}>Create User</button>
      {creationError && <p>{creationError}</p>}
    </div>
  );
};

export default Home;
