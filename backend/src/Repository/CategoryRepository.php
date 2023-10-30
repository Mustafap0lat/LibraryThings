<?php
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
        $category = $this->findOneBy(['categoryName' => $categoryName]);

        
        if (!$category) {
            $category = new Category();
            $category->setCategory($category);
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

    public function saveCategory($user, $book, $category)
    {
        $cat = new Category();
        $cat->setBook($book);
        $cat->setUser($user);
        $cat->setCategory($category);

        $entityManager = $this->getEntityManager();

        $entityManager->persist($cat);
         $entityManager->flush();
    }
}
