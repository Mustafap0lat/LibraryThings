<?php
// src/Repository/NoteRepository.php
namespace App\Repository;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function deleteNoteByIdAndUser(int $noteId, int $userId): bool
{
    $note = $this->findOneBy(['id' => $noteId, 'user' => $userId]);

    if (!$note) {
        return false;
    }

    $em = $this->getEntityManager();
    $em->remove($note);
    $em->flush();

    return true;
}

// src/Repository/NoteRepository.php

public function findNotesByUserAndBook(int $userId, int $bookId): array
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
        'SELECT n
        FROM App\Entity\Note n
        WHERE n.user = :user AND n.book = :book'
    )
    ->setParameter('user', $userId)
    ->setParameter('book', $bookId);

    // returns an array of Note objects
    return $query->getResult();
}

    public function findNotesAndBooksByUser(User $user)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT n, b
            FROM App\Entity\Note n
            JOIN n.book b
            WHERE n.user = :user'
        )->setParameter('user', $user);

        return $query->getResult();
    }

    public function saveNote($user, $book, $noteText)
    {
        $note = new Note();
        $note->setBook($book);
        $note->setUser($user);
        $note->setNoteText($noteText);

        $entityManager = $this->getEntityManager();

        $entityManager->persist($note);
        // Persist the Note too if it's not already managed.
         $entityManager->flush();
    }
}
