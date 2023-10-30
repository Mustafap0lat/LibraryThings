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


class FavoriteBookRepository extends ServiceEntityRepository
{
   
    
    public function __construct(ManagerRegistry $registry)
    {
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
        $qb = $this->createQueryBuilder('fb');
        
        $query = $qb
            ->select('fb', 'u', 'b')
            ->join('fb.user', 'u')
            ->join('fb.book', 'b')
            ->where($qb->expr()->eq('u.username', ':username'))
            ->setParameter('username', $username)
            ->getQuery();


        $favoriteBooks = $query->getResult();

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

    $entityManager = $this->getEntityManager();

    $favoriteBook->setBook($book);

    $note = new Note();
    $note->setNoteText($noteText);
    $note->setUser($user);
    $note->setBook($book);
    $entityManager->persist($favoriteBook);
    $entityManager->flush();
    }

    public function deleteFavoriteBook(FavoriteBook $favoriteBook)
{
    $entityManager = $this->getEntityManager();
    $entityManager->remove($favoriteBook);
    $entityManager->flush();
}

}