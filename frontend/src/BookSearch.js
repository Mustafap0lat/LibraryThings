import React, { useState, useEffect } from "react";
import axios from "axios";

const BookSearch = () => {
  const [query, setQuery] = useState("");
  const [book, setBook] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [favoriteBooks, setFavoriteBooks] = useState([]);
  const [note, setNote] = useState("");
  const [usernames, setUsernames] = useState([]); // corrected from setUsername to setUsernames
  const [selectedUsername, setSelectedUsername] = useState(null);
  const [currentBookId, setCurrentBookId] = useState(null);
  const [rating, setRating] = useState("");
  const [category, setCategory] = useState("");

  const handleNoteChange = (bookId, value) => {
    setNote((prevNotes) => ({
      ...prevNotes,
      [bookId]: value,
    }));
  };

  const handleCategoryChange = (bookId, value) => {
    setCategory((prevCategories) => ({
      ...prevCategories,
      [bookId]: value,
    }));
  };

  const handleRatingChange = (bookId, value) => {
    setRating((prevRatings) => ({
      ...prevRatings,
      [bookId]: value,
    }));
  };

  const fetchBooks = async () => {
    setLoading(true);
    setError("");

    try {
      const response = await axios.get(
        `http://localhost:8000/api/books/${query}`
      );
      console.log(response.data);
      setBook(response.data);
    } catch (error) {
      setError(
        "Failed to fetch data. Please check your connection or try again later."
      );
    } finally {
      setLoading(false);
    }
  };

  const handleSearchInputChanges = (e) => {
    setQuery(e.target.value);
  };

  const callSearchFunction = (e) => {
    e.preventDefault();
    fetchBooks();
  };

  useEffect(() => {
    const fetchUsernames = async () => {
      try {
        const response = await axios.get("http://localhost:8000/api/user/all");
        if (response.data && Array.isArray(response.data)) {
          setUsernames(response.data);
        } else {
          console.error("Unexpected structure of response data", response.data);
        }
      } catch (error) {
        console.error("Failed to fetch usernames", error);
      }
    };

    fetchUsernames();
  }, []);

  const addToFavorites = async () => {
    if (!selectedUsername || !book) {
      console.error("Username or book data is missing!");
      return;
    }

    const bookIsbn = book.isbn_13[0];

    try {
      await axios.post("http://localhost:8000/api/user/addfavorite", {
        userId: selectedUsername.id,
        bookIsbn: bookIsbn,
      });
      fetchFavoriteBooks(selectedUsername);
      setBook(null);
    } catch (error) {
      console.error("There was an error adding the book to favorites!", error);
    }
  };

  const fetchFavoriteBooks = async (username) => {
    try {
      const response = await axios.get(
        `http://localhost:8000/api/user/favorite-bookss/${username}`
      );
      setFavoriteBooks(response.data);

      if (response.data.length > 0) {
        setCurrentBookId(response.data[0].id);
      }
    } catch (error) {
      console.error("Failed to fetch favorite books", error);
    }
  };

  useEffect(() => {
    let interval;

    if (selectedUsername) {
      fetchFavoriteBooks(selectedUsername.username);
      interval = setInterval(() => {
        fetchFavoriteBooks(selectedUsername.username);
      }, 2000);
    }

    return () => {
      if (interval) {
        clearInterval(interval);
      }
    };
  }, [selectedUsername]);

  const deleteFavoriteBook = async (id) => {
    try {
      await axios.delete(`http://localhost:8000/api/user/deletefavorite/${id}`);

      setFavoriteBooks((prevBooks) =>
        prevBooks.filter((book) => book.book.id !== id)
      );
    } catch (error) {
      console.error("There was an error deleting the book!", error);
    }
  };

  const saveNote = async (bookId) => {
    if (!selectedUsername) {
      alert("Please select a user to proceed.");
      return;
    }

    if (!bookId) {
      alert("Please select a book to proceed.");
      return;
    }

    const noteToSave = note[bookId];
    if (!noteToSave.trim()) {
      alert("Please enter a note to proceed.");
      return;
    }

    try {
      const response = await axios.post(
        "http://localhost:8000/api/user/savenote",
        {
          userId: selectedUsername.id,
          bookId: bookId,
          note: noteToSave,
        }
      );

      if (response.status === 201) {
        setNote(""); // Clear the note input field
      } else {
        alert("Failed to save note");
      }
    } catch (error) {
      console.error("There was an error saving the note!", error);
      alert("There was an error saving the note!");
    }
  };

  const saveRating = async (bookId) => {
    if (!selectedUsername) {
      alert("Please select a user to proceed.");
      return;
    }

    if (!bookId) {
      alert("Please select a book to proceed.");
      return;
    }

    const ratingToSave = rating[bookId];
    const trimmedRating = (ratingToSave || "").trim();
    if (!trimmedRating) {
      alert("Please enter a rating to proceed.");
      return;
    }

    try {
      const response = await axios.post(
        "http://localhost:8000/api/user/saverating",
        {
          userId: selectedUsername.id,
          bookId: bookId,
          rate: trimmedRating,
        }
      );

      if (response.status === 201) {
        setRating(""); // Clear the rating input field
      } else {
        alert("Failed to save rating");
      }
    } catch (error) {
      console.error("There was an error saving the rating!", error);
      alert("There was an error saving the rating!");
    }
  };

  const saveCategory = async (bookId) => {
    if (!selectedUsername) {
      alert("Please select a user to proceed.");
      return;
    }

    if (!bookId) {
      alert("Please select a book to proceed.");
      return;
    }

    const categoryToSave = category[bookId];
    if (!categoryToSave.trim()) {
      alert("Please enter a category to proceed.");
      return;
    }

    try {
      const response = await axios.post(
        "http://localhost:8000/api/user/savecategory",
        {
          userId: selectedUsername.id,
          bookId: bookId,
          categoryName: categoryToSave,
        }
      );

      if (response.status === 201) {
        setCategory(""); // Clear the category input field
      } else {
        alert("Failed to save category");
      }
    } catch (error) {
      console.error("There was an error saving the category!", error);
      alert("There was an error saving the category!");
    }
  };

  return (
    <div className="container">
      <div className="search-container">
        <form onSubmit={callSearchFunction} className="input-field">
          <input
            value={query}
            onChange={handleSearchInputChanges}
            type="text"
            placeholder="Enter ISBN 13"
          />
          <button type="submit">SEARCH</button>
        </form>
      </div>

      <div className="book-info-container">
        {loading && (
          <div className="loading-container">
            <div className="loading-ring"></div>
          </div>
        )}

        {error && <p>{error}</p>}

        {book && (
          <div className="book-info">
            <h3>Title:</h3>
            <p>{book.title ? book.title : "No title available."}</p>
            <h3>ISBN:</h3>
            <p>{book.isbn_13 ? book.isbn_13 : "No title available."}</p>
            <button onClick={addToFavorites}>Add to Favorites</button>
          </div>
        )}
      </div>
      <div className="select-container">
        <select
          value={selectedUsername ? selectedUsername.id : ""}
          onChange={(e) => {
            const selectedUserId = e.target.value;
            const userObj = usernames.find(
              (user) => user.id.toString() === selectedUserId
            );
            setSelectedUsername(userObj);
          }}
        >
          <option value="">Select a user</option>
          {usernames.map((user) => (
            <option key={user.id} value={user.id}>
              {user.username}
            </option>
          ))}
        </select>
      </div>

      <div className="favorite-books-container">
        {favoriteBooks.length > 0 ? (
          <div>
            <h3 className="favorite-books-title">Favorite Books:</h3>
            <ul>
              {favoriteBooks.map((book) => (
                <li key={book.book.id} className="favorite-book-item">
                  <div className="book-details">
                    <div className="book-title">{book.book.title}</div>

                    <div className="categories-section">
                      {book.category && book.category.length > 0 ? (
                        <p>
                          <span className="hardcoded-text">Categories: </span>
                          {book.category
                            .map((category) => category.category)
                            .join(", ")}
                        </p>
                      ) : (
                        "No categories"
                      )}
                      <div className="button-input-group">
                        <input
                          type="text"
                          value={category[book.book.id] || ""}
                          onChange={(e) =>
                            handleCategoryChange(book.book.id, e.target.value)
                          }
                          placeholder="Enter your category here"
                          className="input-text"
                        />
                        <button onClick={() => saveCategory(book.book.id)}>
                          Add Category
                        </button>
                      </div>
                    </div>

                    <div className="notes-section">
                      <p>
                        <span className="hardcoded-text">Notes : </span>
                        {book.notes && book.notes.length > 0
                          ? book.notes.map((note) => note.noteText).join(", ")
                          : "No notes"}
                      </p>
                      <div className="button-input-group">
                        <input
                          type="text"
                          value={note[book.book.id] || ""}
                          onChange={(e) =>
                            handleNoteChange(book.book.id, e.target.value)
                          }
                          placeholder="Enter your note here"
                          className="input-text"
                        />
                        <button onClick={() => saveNote(book.book.id)}>
                          Add Note
                        </button>
                      </div>
                    </div>

                    <div className="ratings-section">
                      <p>
                        <span className="hardcoded-text">Ratings : </span>
                        {book.rating && book.rating.length > 0
                          ? book.rating
                              .map((rating) => rating.rating)
                              .join(", ")
                          : "No ratings"}
                      </p>
                      <div className="button-input-group">
                        <select
                          value={rating[book.book.id] || ""}
                          onChange={(e) =>
                            handleRatingChange(book.book.id, e.target.value)
                          }
                          className="input-select"
                        >
                          <option value="" disabled>
                            Select your rating
                          </option>
                          {Array.from({ length: 10 }, (_, i) => (
                            <option key={i + 1} value={String(i + 1)}>
                              {i + 1}
                            </option>
                          ))}
                        </select>
                        <button onClick={() => saveRating(book.book.id)}>
                          Add Rating
                        </button>
                      </div>
                    </div>
                    <button
                      className="delete-button"
                      onClick={() => deleteFavoriteBook(book.book.id)}
                    >
                      Delete
                    </button>
                  </div>
                </li>
              ))}
            </ul>
          </div>
        ) : (
          <p className="no-favorites-message">No favorite books to display.</p>
        )}
      </div>
    </div>
  );
};

export default BookSearch;
