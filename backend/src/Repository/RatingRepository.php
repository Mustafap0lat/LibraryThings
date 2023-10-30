<?php
namespace App\Repository;

use App\Entity\Rating;
use App\Entity\User;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    public function deleteRatingByIdAndUser(int $ratingId, int $userId): bool
{
    $rating = $this->findOneBy(['id' => $ratingId, 'user' => $userId]);

    if (!$rating) {
        return false;
    }

    $em = $this->getEntityManager();
    $em->remove($rating);
    $em->flush();

    return true;
}


public function findRatingsByUserAndBook(int $userId, int $bookId): array
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
        'SELECT n
        FROM App\Entity\Rating n
        WHERE n.user = :user AND n.book = :book'
    )
    ->setParameter('user', $userId)
    ->setParameter('book', $bookId);

    return $query->getResult();
}

    public function findRatingsAndBooksByUser(User $user)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT n, b
            FROM App\Entity\Rating n
            JOIN n.book b
            WHERE n.user = :user'
        )->setParameter('user', $user);

        return $query->getResult();
    }

    public function saveRating($user, $book, $rating)
    {
        $rate = new Rating();
        $rate->setBook($book);
        $rate->setUser($user);
        $rate->setRating($rating);

        $entityManager = $this->getEntityManager();

        $entityManager->persist($rate);
         $entityManager->flush();
    }
}
