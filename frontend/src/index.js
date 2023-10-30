import React from "react";
import ReactDOM from "react-dom";
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { useNavigate } from 'react-router-dom';

import "./styles/index.css";
import BookSearch from './BookSearch'; // make sure the path is correct
import Home from "./Home";

const App = () => {
  const navigate = useNavigate();

  const handleUserCreated = () => {
    navigate("/book-search");
  };

  const handleAlreadyRegistered = () => {
    navigate("/book-search");
  };

  return (
    <React.StrictMode>
      <div>
        <Routes>
          <Route
            path="/"
            element={
              <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
              <Home onUserCreated={handleUserCreated} />
              <button className="already-registered-button" onClick={handleAlreadyRegistered}>Already Registered</button>
            </div>
            }
          />
          <Route path="/book-search" element={<BookSearch />} />
        </Routes>
      </div>
    </React.StrictMode>
  );
};

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
  <Router>
    <App />
  </Router>
);
