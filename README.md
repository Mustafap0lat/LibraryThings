# Work sample
When all required tools are installed, run `make init` in [root](./) directory. This will install all node modules and also all composer packages. We will provide the .env file for the backend. Remember to implement correct error handling.
## Frontend
The frontend requires node v18.18.1

To start the frontend run `make frontend` in the [root](./) directory.

## Backend
The backend requires php@7.4 and both composer version 2 and symfony cli installed.

To start the backend run `make backend` in the [root](./) directory.

## Assignment
The frontend and backend should continue to remain in separate solutions.

### Frontend
The frontend should be able to search for books from the **OpenLibrary's** API, remember all API calls should go through our backend. This way it's easier to cache the requests. Whenever a user adds a book to his/her favorite collection you should save the book and the author in our database and then allow the user to add notes and categories to the book. The user should also be able to create new categories and grade the book. The frontend has no boilerplate and you're free to do it however you like, but remember it should be functional and easy to use. Display users favorite books in a table with a search feature. To fetch the data from the backend use fetch or axios. For components, feel free to use a library or create them yourself. But at least some components should be self-made. For styling, you can use ordinary css, sass or styled-components. Create functions, components, states, contexts, routes and so on as necessary.
### Backend
In the list below you find the assignments. There is a boiler plate created. All controllers are prefixed with api. All responses should be returned in application/json. So the entities and models need to be serialized. Continue to create controllers, entities, services and events if needed. Also feel free to come with you're own improvements. The base url for OpenLibrary is https://openlibrary.org and sending the header application/json header returns the result in json. So use Symfonys http client or curl.
1. Continue the [OpenLibraryClient](./backend/src/Service/OpenLibraryClient.php) to be able to fetch data from [OpenLibrary's API](https://openlibrary.org/developers/api) and map the raw JSON to a model. You can use JMS serializer or Symfonys own serializer. And register the service in [services](./backend/config/services.yaml). Use dependency injection wherever you need the service.
2. Continue to add fields to both the book and the author entity.
3. Create an entity for favorite books with a relationship to the book entity and continue the [BooksController](./backend/src/Controller/BooksController.php) to allow the user to add and remove favorite books.
4. Create an entity to manage categories for the books using the appropriate relationship.
5. Create an entity to manage user notes on the book using the appropriate relationship.
6. Create DTO objects if necessary.

## Final words
We at Logy wishes you good luck and if you have any questions or feedback, don't hesitate to contact us.