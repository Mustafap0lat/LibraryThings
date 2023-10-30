<?php

namespace App\Controller;

use App\Repository\FavoriteBookRepository;
use App\Repository\UserRepository;
use App\Repository\NoteRepository;
use App\Repository\CategoryRepository;
use App\Repository\RatingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OpenLibraryClient;
use App\Entity\FavoriteBook;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Entity\Book;
use App\Entity\Note;
use App\Entity\Category;
use App\Entity\Rating;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;

//UNCOMMENT WHEN RUNNING
// header("Access-Control-Allow-Origin: http://localhost:3000"); 
// header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS"); 
// header("Access-Control-Allow-Headers: Content-Type, Authorization"); 

// if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//     exit; 
// }

class UserController extends AbstractController
{
    private FavoriteBookRepository $repository;
    private UserRepository $userRepository;
    private OpenLibraryClient $openLibraryService;
    private LoggerInterface $logger;

    public function __construct(FavoriteBookRepository $repository, LoggerInterface $logger, OpenLibraryClient $openLibraryService, UserRepository $userRepository) {
        $this->repository = $repository;
        $this->openLibraryService = $openLibraryService;
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/user/create/{username}", name="app_user")
     */
    public function create(string $username): JsonResponse
    {

        $userExists = $this->userRepository->findByUsername($username);
        if($userExists){
            return $this->json([
                'error' => 'Username already exists',
            ], 400);
        }

        $user = new User();
        $user->setUsername($username);

        $this->userRepository->addUser($user, true);

        return $this->json($user);
    }


/**
 * @Route("/user/all", name="app_all_users", methods={"GET"})
 */
public function getAllUsers(): JsonResponse
{
    $users = $this->userRepository->findAll();

    $usersArray = [];
    foreach ($users as $user) {
        $usersArray[] = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
        ];
    }

    return $this->json($usersArray);
}

    /**
     * @Route("/user/favorite-bookss/{username}", name="favorite_books_by_username", methods={"GET"})
     */
    public function getFavoriteBooksByUsernames(string $username, UserRepository $userRepository, SerializerInterface $serializer)
    {
        $user = $userRepository->findOneBy(['username' => $username]);

        
        if (!$user) {
            $this->logger->info("no user found");
            return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->logger->debug("BLA BLA");
        $favoriteBooks = $user->getFavoriteBooks();

        $serializedData = [];

        foreach ($favoriteBooks as $book) {
            $bookData = [
                'book' => $book,
                'notes' => $this->getNotesForUserAndBook($user, $book, $serializer),
                'rating' => $this->getRatingsForUserAndBook($user, $book, $serializer),
                'category' => $this->getCategoryForUserAndBook($user, $book, $serializer)
            ];
    
            $serializedData[] = $bookData;
        }
        

        $json = $serializer->serialize($serializedData, 'json', ['groups' => 'public']);
            return new JsonResponse($json, 200, [], true);
    }

    private function getNotesForUserAndBook(User $user, Book $book, SerializerInterface $serializer): array
    {
            $this->logger->info("NOTE NOTE NOTE");
            $notes = $this->getDoctrine()->getRepository(Note::class)->findBy([
                'user' => $user,
                'book' => $book,
            ]);

            $this->logger->info("NOTE NOTE NOTE 22222");

            $serializedNotes = [];

            foreach ($notes as $note) {
                $serializedNotes[] = $note;
            }

            return $serializedNotes;
    }

    private function getRatingsForUserAndBook(User $user, Book $book, SerializerInterface $serializer): array
    {
            $ratings = $this->getDoctrine()->getRepository(Rating::class)->findBy([
                'user' => $user,
                'book' => $book,
            ]);
            $serializedRatings = [];

            foreach ($ratings as $rating) {
                $serializedRatings[] = $rating;
            }

            return $serializedRatings;
    }
    private function getCategoryForUserAndBook(User $user, Book $book, SerializerInterface $serializer): array
    {
            $categories = $this->getDoctrine()->getRepository(Category::class)->findBy([
                'user' => $user,
                'book' => $book,
            ]);

            $serializedCategories = [];

            foreach ($categories as $category) {
                $serializedCategories[] = $category;
            }

            return $serializedCategories;
    }



    /**
     * @Route("/user/addfavorite", name="app_add_favorite", methods={"POST"})
     */
    public function saveFavoriteBook(Request $request): JsonResponse
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);
        
        $userId = $data['userId'] ?? null;
        $bookIsbn = $data['bookIsbn'] ?? null;
     

        $this->logger->info((string)$userId);
        $this->logger->info((string)$bookIsbn);


        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findById($userId);
        $this->logger->info("we came far");
        $book = $entityManager->getRepository(Book::class)->getByIsbn($bookIsbn);
        $this->logger->info("we came far3");     

        
        if (!$user || !$book) {
            $fetchedBook = $this->openLibraryService->getBookByIsbn($bookIsbn);

            $book = new Book();
            $this->logger->info("far 44");
            $book->setTitle($fetchedBook->getTitle());
            $book->setIsbn((string)$fetchedBook->getIsbn_13()[0]);

            $this->logger->info("Book title ===================");
            $this->logger->info($book->getTitle());

        }

        $this->logger->info("far 45");
        $this->logger->info($book->getTitle());

      

        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $this->logger->info("far 46");
        $userRepository->saveAsFavoriteBook($user, $book);

        return $this->json(['message' => 'Favorite book saved successfully'], JsonResponse::HTTP_CREATED);

    }

    /**
     * @Route("/user/savenote", name="app_add_notes", methods={"POST"})
     */
    public function saveNoteForBook(Request $request): JsonResponse
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);
        
        $userId = $data['userId'] ?? null;
        $bookId = $data['bookId'] ?? null;
        $note = $data['note'] ?? null;

        $this->logger->info($note);

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findById($userId);
        $book = $entityManager->getRepository(Book::class)->getById($bookId);   

        $noteRepository = $this->getDoctrine()->getRepository(Note::class);

        
        $noteRepository->saveNote($user, $book, $note);

        return $this->json(['message' => 'Note saved successfully'], JsonResponse::HTTP_CREATED);

    }
    
    /**
     * @Route("/user/saverating", name="app_add_rating", methods={"POST"})
     */
    public function saveRatingForBook(Request $request): JsonResponse
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);
        
        $userId = $data['userId'] ?? null;
        $bookId = $data['bookId'] ?? null;
        $rating = $data['rate'] ?? null;

        $this->logger->info($rating);

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findById($userId);
        $book = $entityManager->getRepository(Book::class)->getById($bookId);   

        $ratingRepository = $this->getDoctrine()->getRepository(Rating::class);

        
        $ratingRepository->saveRating($user, $book, $rating);

        return $this->json(['message' => 'Rating saved successfully'], JsonResponse::HTTP_CREATED);

    }
    
    /**
     * @Route("/user/savecategory", name="app_add_category", methods={"POST"})
     */
    public function saveCategoryForBook(Request $request): JsonResponse
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);
        
        $userId = $data['userId'] ?? null;
        $bookId = $data['bookId'] ?? null;
        $category = $data['categoryName'] ?? null;

        $this->logger->info($category);

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findById($userId);
        $book = $entityManager->getRepository(Book::class)->getById($bookId);   

        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

        
        $categoryRepository->saveCategory($user, $book, $category);

        return $this->json(['message' => 'Category saved successfully'], JsonResponse::HTTP_CREATED);

    }

    /**
     * @Route("/user/favorite-books/{username}", name="app_user_favorite_books", methods={"GET"})
     */
    public function getFavoriteBooksByUsername(string $username): JsonResponse
    {
        $this->logger->info("Fetching favorite books for username: $username");

        $favoriteBooks = $this->repository->findAllFavoriteBooksByUsername($username);

        $defaultContext = [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
            'enable_max_depth' => true,
        ];

        return $this->json($favoriteBooks, 200, [], $defaultContext);
    }

/**
 * @Route("/user/deletefavorite/{id}", name="app_delete_favorite", methods={"DELETE"})
 */
public function deleteFavoriteBook(int $id): JsonResponse
{
    $entityManager = $this->getDoctrine()->getManager();
    $book = $entityManager->getRepository(Book::class)->find($id);

    if (!$book) {
        return $this->json(['error' => 'Favorite book not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $entityManager->remove($book);
    $entityManager->flush();

    return $this->json(['message' => 'Favorite book deleted successfully'], JsonResponse::HTTP_OK);
}

    
/**
 * @Route("/user/addnote", name="app_add_note", methods={"POST"})
 */
public function addNoteToBook(Request $request): JsonResponse
{
    $jsonData = $request->getContent();
    $data = json_decode($jsonData, true);

    $userId = $data['userId'] ?? null;
    $bookId = $data['bookId'] ?? null;
    $noteText = $data['noteText'] ?? null;

    if (null === $userId || null === $bookId || null === $noteText) {
        return $this->json(['error' => 'Invalid data'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $entityManager = $this->getDoctrine()->getManager();

    $user = $entityManager->getRepository(User::class)->find($userId);

    if (!$user) {
        return $this->json(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $book = $entityManager->getRepository(Book::class)->find($bookId);

    if (!$book) {
        return $this->json(['error' => 'Book not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $note = new Note();
    $note->setNoteText($noteText);
    $note->setBook($book);
    $note->setUser($user);

    $entityManager->persist($note);
    $entityManager->flush();

    return $this->json(['message' => 'Note added to book successfully'], JsonResponse::HTTP_CREATED);
}


/**
 * @Route("/user/{userId}/notes/{noteId}", name="delete_user_note", methods={"DELETE"})
 */
public function deleteUserNote(int $userId, int $noteId, NoteRepository $noteRepository): JsonResponse
{
    $result = $noteRepository->deleteNoteByIdAndUser($noteId, $userId);

    if (!$result) {
        return $this->json(['error' => 'Note not found or you\'re not authorized to delete it'], JsonResponse::HTTP_NOT_FOUND);
    }

    return $this->json(['message' => 'Note deleted successfully'], JsonResponse::HTTP_OK);
}


/**
 * @Route("/user/{userId}/book/{bookId}/notes", name="app_user_book_notes", methods={"GET"})
 */
public function getUserBookNotes(int $userId, int $bookId, NoteRepository $noteRepository): JsonResponse
{
    $notes = $noteRepository->findNotesByUserAndBook($userId, $bookId);

    $notesArray = [];
    foreach ($notes as $note) {
        $notesArray[] = [
            'id' => $note->getId(),
            'text' => $note->getNoteText(),
        ];
    }

    return $this->json($notesArray);
}

    /**
     * @Route("/user/{userId}/notes/books", name="app_user_notes_books", methods={"GET"})
     */
    public function getUserNotesWithBooks(int $userId, NoteRepository $noteRepository): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            return $this->json(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $notes = $noteRepository->findNotesAndBooksByUser($user);

        $notesArray = [];
        foreach ($notes as $note) {
            $book = $note->getBook();
            $notesArray[] = [
                'note_id' => $note->getId(),
                'note_text' => $note->getText(),
                'book_id' => $book->getId(),
                'book_title' => $book->getTitle(),
            ];
        }

        return $this->json($notesArray);
    }

    /**
     * @Route("/book/addcategory", name="app_add_category_to_book", methods={"POST"})
     */
    public function addCategoryToBook(Request $request): JsonResponse
    {

        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        $userId = $data['userId'] ?? null;
        $bookId = $data['bookId'] ?? null;
        $categoryName = $data['categoryName'] ?? null;

        if (null === $userId || null === $bookId || null === $categoryName) {
            return $this->json(['error' => 'Invalid data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            return $this->json(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $book = $entityManager->getRepository(Book::class)->find($bookId);

        if (!$book) {
            return $this->json(['error' => 'Book not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $category = new Category();
        $category->setName($categoryName);
        $category->setBook($book);
        $category->setUser($user);

        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json(['message' => 'Note added to book successfully'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/user/{userId}/category/books", name="app_user_category_books", methods={"GET"})
     */
    public function getUserCategoryWithBooks(int $userId, CategoryRepository $categoryRepository): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            return $this->json(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $categories = $categoryRepository->findCategoryAndBooksByUser($user);

        $categoryArray = [];
        foreach ($categories as $category) {
            $book = $category->getBook();
            $categoryArray[] = [
                'category_id' => $category->getId(),
                'category_name' => $category->getName(),
                'book_id' => $book->getId(),
                'book_title' => $book->getTitle(),
            ];
        }

        return $this->json($categoryArray);
    }
    
    /**
     * @Route("/books/category", name="create_book", methods={"POST"})
     */
    public function createBookWithCategory(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['isbn'], $data['categories'])) {
            return $this->json(['message' => 'Missing parameters'], Response::HTTP_BAD_REQUEST);
        }

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);

        foreach ($data['categories'] as $catName) {
            $category = new Category();
            $category->setName($catName);
            $book->addCategory($category);
        }

        $em->persist($book);
        $em->flush();

        return $this->json(['message' => 'Book created successfully'], Response::HTTP_CREATED);
    }
}


