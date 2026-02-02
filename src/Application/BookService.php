<?php
namespace OpenBook\Application;
use OpenBook\Domain\Book;

use OpenBook\Infrastructure\BookRepository;
use OpenBook\Infrastructure\BookApiClient;

class BookService
{
    private BookRepository $repository;
    private BookApiClient $apiClient;

    public function __construct(BookRepository $repository, BookApiClient $apiClient)
    {
        $this->repository = $repository;
        $this->apiClient = $apiClient;
    }

    public function createBook(Book $book): Book
    {
        // Llamamos a la API externa
        $externalData = $this->apiClient->fetchBookData($book->isbn);

        if ($externalData) {
            $book->description = $externalData['description'] ?? null;
            $book->coverUrl = $externalData['coverUrl'] ?? null;
            $book->author = $externalData['authors'][0] ?? $book->author;
            $book->title = $externalData['title'] ?? $book->title;

        }

        // Guardamos el libro en la base de datos
        return $this->repository->save($book);
    }




    public function listBooks(int $page = 1, int $limit = 5)
    {
        $books = $this->repository->findPaginated($page, $limit);
        $total = $this->repository->countTotal();

        return [
            'books' => $books,
            'totalPages' => ceil($total / $limit),
            'currentPage' => $page,
            'totalCount' => $total
        ];
    }

    public function searchBooks(string $query)
    {
        return $this->repository->search($query);
    }

    public function getBookById(int $id): ?array
    {
        return $this->repository->getBookById($id);
    }
    public function updateBook($id, $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteBook($id)
    {
        return $this->repository->delete($id);
    }
}
