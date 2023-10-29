<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\FavoriteBook;
use App\Entity\User;
use App\Entity\Book;
use App\Entity\Note;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\FavoriteBookRepository;
use Psr\Log\LoggerInterface;

class FavoriteBookRepository extends ServiceEntityRepository
{
    private LoggerInterface $logger;
    
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct($registry, FavoriteBook::class);
    }

    /**
     *
     * @param string $username
     *
     * @return FavoriteBook[]
     */
    public function findAllFavoriteBooksByUsername(string $username): array
    {
        $qb = $this->createQueryBuilder('fb');  // fb is alias for FavoriteBook
        
        $query = $qb
            ->select('fb', 'u', 'b')  // Selecting FavoriteBook, User, and Book entities
            ->join('fb.user', 'u')  // Joining User on FavoriteBook
            ->join('fb.book', 'b')  // Joining Book on FavoriteBook
            ->where($qb->expr()->eq('u.username', ':username'))  // Where username equals the passed username
            ->setParameter('username', $username)
            ->getQuery();


        $favoriteBooks = $query->getResult(); // Execute the query and get results

        // $noteRepository = $this->getEntityManager()->getRepository(Note::class);
        // foreach ($favoriteBooks as $favoriteBook) {
        //     $notes = $noteRepository->findBy(['favoriteBook' => $favoriteBook]);
        //     $favoriteBook->setNotes($notes);
        // }    
    
        return $favoriteBooks;
    }
    
    
    

    /**
     * Save a FavoriteBook entity along with a Note.
     *
     * @param User $user
     * @param Book $book
     */
    public function saveFavoriteBookWithNote(User $user, Book $book)
    {
        // $favoriteBook = new FavoriteBook();
        // $favoriteBook->addUser($user);
        // $favoriteBook->addBook($book);

        // $test = addBook($book);
        
        
        // // Set the Note for the FavoriteBook.
        // $favoriteBook->addNote($note);

        // // Persist the FavoriteBook entity along with the Note.
        // $entityManager = $this->getEntityManager();
        // $entityManager->persist($favoriteBook);
        // $entityManager->persist($note); // Persist the Note too if it's not already managed.
        // $entityManager->flush();

        $entityManager = $this->getEntityManager();

    // Create a new FavoriteBook
    // $favoriteBook = new FavoriteBook();
    // $cat = new Category();
    // $cat->setCategoryName("Action");

    // Add the User to the FavoriteBook
    //$favoriteBook->setUser($user);

    // Set the title of the FavoriteBook based on the Book's title
    $favoriteBook->setBook($book);

    // $favoriteBook->setRating(3);


    $note = new Note();
    $note->setNoteText($noteText);
    $note->setUser($user);
    $note->setBook($book);
    // // Add the Note to the FavoriteBook
    // $favoriteBook->addNote($note);

    // Persist the FavoriteBook entity along with the Note.
    $entityManager->persist($favoriteBook);
   // Persist the Note too if it's not already managed.
    $entityManager->flush();
    }

    public function deleteFavoriteBook(FavoriteBook $favoriteBook)
{
    $entityManager = $this->getEntityManager();
    $entityManager->remove($favoriteBook);
    $entityManager->flush();
}

}