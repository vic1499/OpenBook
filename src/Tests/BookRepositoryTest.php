<?php
namespace OpenBook\Tests;

use OpenBook\Domain\Book;
use OpenBook\Infrastructure\BookRepository;
use PHPUnit\Framework\TestCase;

class BookRepositoryTest extends TestCase
{
    private BookRepository $repository;

    protected function setUp(): void
    {
        // Usamos SQLite en memoria para que los tests no toquen la base real
        $this->repository = new BookRepository(':memory:');
    }

    public function testSaveAndFindById(): void
    {
        $book = new Book(
            id: null,
            title: "Test Libro",
            author: "Autor Test",
            isbn: "1234567890"
        );

        // Guardar libro
        $savedBook = $this->repository->save($book);

        $this->assertNotNull($savedBook->id, "El ID no debería ser null tras guardar");

        // Recuperar por ID
        $fetchedBook = $this->repository->findById($savedBook->id);
        $this->assertSame("Test Libro", $fetchedBook->title);
        $this->assertSame("Autor Test", $fetchedBook->author);
        $this->assertSame("1234567890", $fetchedBook->isbn);

        // Limpiar: eliminar libro
        $this->repository->delete($savedBook->id);
    }

    public function testFindAll(): void
    {
        // Inicialmente vacío
        $books = $this->repository->findAll();
        $this->assertIsArray($books);
        $this->assertCount(0, $books);

        // Agregar un libro
        $book = new Book(id: null, title: "Libro1", author: "Autor1", isbn: "1111111111");
        $this->repository->save($book);

        $booksAfter = $this->repository->findAll();
        $this->assertCount(1, $booksAfter);
        $this->assertSame("Libro1", $booksAfter[0]->title);
    }
}
