<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OpenLibraryClient;
use App\Entity\FavoriteBook;

class BooksController extends AbstractController
{
    private BookRepository $repository;
    private OpenLibraryClient $openLibraryService;

    public function __construct(BookRepository $repository, OpenLibraryClient $openLibraryService) {
        $this->repository = $repository;
        $this->openLibraryService = $openLibraryService;
    }
    
    /**
     * @Route("/books/{isbn}", name="app_books")
     */
    public function index(string $isbn): JsonResponse
    {
        $book = $this->openLibraryService->getBookByIsbn($isbn);
        
        if ($book === null) {
            return $this->json([
                'error' => 'Book not found',
            ], 404);
        }

        return $this->json($book);
    }


}
