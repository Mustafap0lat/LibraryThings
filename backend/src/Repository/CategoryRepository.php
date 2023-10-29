<?php
// src/Repository/CategoryRepository.php
namespace App\Repository;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    
    public function findOrCreateByName(string $categoryName): Category
    {
        $category = $this->findOneBy(['name' => $categoryName]);
        
        if (!$category) {
            $category = new Category();
            $category->setName($categoryName);
            $this->_em->persist($category);
            $this->_em->flush();
        }

        return $category;
    }

    
public function findCategoryByUserAndBook(int $userId, int $bookId): array
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
        'SELECT n
        FROM App\Entity\Category n
        WHERE n.user = :user AND n.book = :book'
    )
    ->setParameter('user', $userId)
    ->setParameter('book', $bookId);

    // returns an array of Note objects
    return $query->getResult();
}

    public function findCategoryAndBooksByUser(User $user)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT n, b
            FROM App\Entity\Category n
            JOIN n.book b
            WHERE n.user = :user'
        )->setParameter('user', $user);

        return $query->getResult();
    }
}
