import React, { useState, useEffect } from "react";
import axios from "axios";

const BookSearch = () => {
  const [query, setQuery] = useState("");
  const [book, setBook] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [favoriteBooks, setFavoriteBooks] = useState([]);
  const [noteText, setNoteText] = useState("");
  const [userNotes, setUserNotes] = useState([]);
  const [selectedBookIdForNote, setSelectedBookIdForNote] = useState(null);
  const [usernames, setUsernames] = useState([]); // corrected from setUsername to setUsernames
  const [selectedUsername, setSelectedUsername] = useState(null);
  const [currentBookId, setCurrentBookId] = useState(null);
  const [selectedBookIdForCategory, setSelectedBookIdForCategory] =
    useState(null);
  const [categoryName, setCategoryName] = useState("");
  const [userCategoriesWithBooks, setUserCategoriesWithBooks] = useState([]);
  const [userCategory, setUserCategory] = useState([]);

  // const fetchUserCategoriesWithBooks = async (userId) => {
  //   try {
  //     const response = await axios.get(
  //       `http://localhost:8000/api/user/${userId}/category/books`
  //     );
  //     setUserCategory(response.data);
  //   } catch (error) {
  //     console.error("Failed to fetch user categories and books", error);
  //   }
  // };

  // useEffect(() => {
  //   if (selectedUsername) {
  //     fetchUserCategoriesWithBooks(selectedUsername.id); // This is our new fetch
  //     fetchFavoriteBooks(selectedUsername.username);
  //   }
  // }, [selectedUsername]);

  console.log(favoriteBooks)

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
          // Assuming the response structure is { userId: "xxx", username: "xxx" }
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

    // Ensure you're sending a string for ISBN, not an array
    const bookIsbn = book.isbn_13[0]; // considering isbn_13 is an array

    try {
      await axios.post("http://localhost:8000/api/user/addfavorite", {
        userId: selectedUsername.id,
        bookIsbn: bookIsbn,
      });
      alert("Book added to favorites");
      fetchFavoriteBooks(selectedUsername)
    } catch (error) {
      console.error("There was an error adding the book to favorites!", error);
    }
  };

  const fetchFavoriteBooks = async (username) => {
    try {
      const response = await axios.get(
        `http://localhost:8000/api/user/favorite-bookss/${username}`
      );
      setFavoriteBooks(response.data); // assuming the data is an array of book objects

      // Set the currentBookId from the first book in the response
      // This assumes that the book's ID property is 'id'
      if (response.data.length > 0) {
        setCurrentBookId(response.data[0].id);
      }
    } catch (error) {
      console.error("Failed to fetch favorite books", error);
    }
  };

  useEffect(() => {
    if (selectedUsername) {
      fetchFavoriteBooks(selectedUsername.username);
    }
  }, [selectedUsername]);

  const deleteFavoriteBook = async (bookId) => {
    try {
      await axios.delete(
        `http://localhost:8000/api/user/deletefavorite/${bookId}`
      );
      // If successful, remove the book from the local state to update the UI
      setFavoriteBooks(favoriteBooks.filter((book) => book.id !== bookId));
    } catch (error) {
      console.error("There was an error deleting the book!", error);
    }
  };

  const addNoteToBook = async () => {
    if (!selectedUsername || !selectedBookIdForNote) {
      console.error("Username or book data is missing!");
      return;
    }

    // Find the correct book ID
    // This assumes favoriteBooks includes the nested structure and
    // selectedBookIdForNote refers to the favorite book's ID (e.g., 5 in your example)
    const favoriteBook = favoriteBooks.find(
      (book) => book.id === selectedBookIdForNote
    );

    let nestedBookId = null;
    if (favoriteBook && favoriteBook.books && favoriteBook.books.length > 0) {
      // Assuming you want the first book in the "books" array
      nestedBookId = favoriteBook.books[0].id; // Adjust if it's not always the first book
    }

    if (!nestedBookId) {
      console.error("Nested book ID not found!");
      return;
    }

    try {
      await axios.post("http://localhost:8000/api/user/addnote", {
        userId: selectedUsername.id, // assuming this is correct from your context
        bookId: nestedBookId, // using the nested book ID here
        noteText: noteText,
      });
      alert("Note added to book successfully");
      setNoteText(""); // clear the note text field after successful posting
      setSelectedBookIdForNote(null); // reset the selected book for note
    } catch (error) {
      console.error("There was an error adding the note!", error);
    }
  };

  // const fetchUserNotesWithBooks = async (userId) => {
  //   try {
  //     const response = await axios.get(
  //       `http://localhost:8000/api/user/${userId}/notes/books` // adjust this URL to your API's endpoint
  //     );
  //     setUserNotes(response.data); // assuming the data is an array of notes with book data
  //     console.log(response.data)
  //   } catch (error) {
  //     console.error("Failed to fetch user notes and books", error);
  //   }
  // };

  // //Fetch notes when a new user is selected
  // useEffect(() => {
  //   if (selectedUsername) {
  //     fetchUserNotesWithBooks(selectedUsername.id); // assuming the user object has an 'id' field
  //     fetchFavoriteBooks(selectedUsername.username); // existing call, don't remove this
  //   }
  // }, [selectedUsername]);

  const addCategoryToBook = async () => {
    if (!selectedUsername || !selectedBookIdForCategory) {
      console.error("Username or book data is missing!");
      return;
    }

    // Find the correct book ID using the selectedBookIdForCategory
    const favoriteBook = favoriteBooks.find(
      (book) => book.id === selectedBookIdForCategory
    );

    let nestedBookId = null;
    if (favoriteBook && favoriteBook.books && favoriteBook.books.length > 0) {
      nestedBookId = favoriteBook.books[0].id; // Adjust if it's not always the first book
    }

    if (!nestedBookId) {
      console.error("Nested book ID not found!");
      return;
    }

    try {
      // Send the request to the backend
      await axios.post("http://localhost:8000/api/book/addcategory", {
        userId: selectedUsername.id,
        bookId: nestedBookId,
        categoryName: categoryName,
      });

      alert("Category added to book successfully");

      // Update the local state to reflect the change
      setFavoriteBooks((prevBooks) => {
        return prevBooks.map((book) => {
          if (book.id === selectedBookIdForCategory) {
            return {
              ...book,
              categories: [...(book.categories || []), categoryName],
            };
          }
          return book;
        });
      });

      // Clear the input fields and reset state
      setCategoryName("");
      setSelectedBookIdForCategory(null);
    } catch (error) {
      console.error("There was an error adding the category!", error);
    }
  };

  return (
    <div>
      <form onSubmit={callSearchFunction}>
        <input value={query} onChange={handleSearchInputChanges} type="text" />
        <input onClick={callSearchFunction} type="submit" value="SEARCH" />
      </form>

      {loading && <p>Loading...</p>}

      {error && <p>{error}</p>}

      {book && (
        <div>
          {/* Render title */}
          <h3>Title:</h3>
          <p>{book.title ? book.title : "No title available."}</p>

          {/* Render identifiers */}
          <h3>ISBN:</h3>
          <p>{book.isbn_13 ? book.isbn_13 : "No title available."}</p>
          {/* ... rest of the code to render identifiers and authors */}

          {/* Add to Favorites and Remove from Favorites buttons */}
        </div>
      )}
      <select
        value={selectedUsername ? selectedUsername.id : ""} // changed from userId to id
        onChange={(e) => {
          const selectedUserId = e.target.value;
          const userObj = usernames.find(
            (user) => user.id.toString() === selectedUserId
          ); // ensure you compare strings, as value is always a string
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

      <button onClick={addToFavorites}>Add to Favorites</button>


    
      {favoriteBooks.length > 0 ? (
        <div>
  <h3>Favorite Books:</h3>
    <ul>
      {favoriteBooks.map((book) => (
        <li key={book.book.id}>
          {book.book.title} {/* Accessing title */}
          Rating: {book.rating}
          <button onClick={() => deleteFavoriteBook(book.book.id)}>
            Delete
          </button>
          {/* Note Adding and Displaying UI */}
          <input
            type="text"
            value={selectedBookIdForNote === book.book.id ? noteText : ""}
            onChange={(e) => {
              setSelectedBookIdForNote(book.book.id);
              setNoteText(e.target.value);
            }}
            placeholder="Write your note here..."
          />
                <button onClick={() => addNoteToBook(book.id)}>Add Note</button>
                {book.userNotes && book.userNotes.length > 0 ? (
                  <ul>
                    {book.book.notes.map((note, index) =>(
                      <li key={index}>{note}</li>
                      ))}
                  </ul>
                ) : null}
                {/* Category Adding and Displaying UI */}
                <input
                  type="text"
                  value={
                    selectedBookIdForCategory === book.id ? categoryName : ""
                  }
                  onChange={(e) => {
                    setSelectedBookIdForCategory(book.id);
                    setCategoryName(e.target.value);
                  }}
                  placeholder="Write your category here..."
                />
                <button onClick={addCategoryToBook}>Add Category</button>
                {book.categories && book.categories.length > 0 ? (
                  <ul>
                    {book.categories.map((category, index) => (
                      <li key={index}>{category}</li>
                    ))}
                  </ul>
                ) : null}
              </li>
            ))}
          </ul>
          <div>
            
            {userNotes.length > 0 ? (
              <div>
                <ul>
                  {userNotes.map((book) => (
                    <li key={book.book_id}>
                      <p>
                        <strong>Book:</strong> {book.book_title}
                      </p>
                      <p>
                        <strong>Note:</strong> {book.note_text}
                      </p>
                      <p>
                        <strong>Rating:</strong> {book.rating}
                      </p>
                    </li>
                  ))}
                </ul>
              </div>
            ) : (
              <p>No notes to display for this user.</p>
            )}
          </div>
        </div>
      ) : (
        <p>No favorite books to display.</p>
      )}

      <div>
        {userCategory.length > 0 ? (
          <div>
            <ul>
              {userCategory.map((book) => (
                <li key={book.book_id}>
                  <strong>Book:</strong> {book.book_title}
                  <p>
                    <strong>Category:</strong> {book.category_name}
                  </p>
                </li>
              ))}
            </ul>
          </div>
        ) : (
          <p>No categories to display for this user.</p>
        )}
      </div>

    </div>
  );
};

export default BookSearch;
