<?php
namespace OpenBook\Tests;

use PHPUnit\Framework\TestCase;
use OpenBook\Application\BookService;
use OpenBook\Domain\Book;
use OpenBook\Infrastructure\BookRepository;
use OpenBook\Infrastructure\BookApiClient;

class BookServiceTest extends TestCase
{
    private BookRepository $repository;
    private BookApiClient $apiClient;
    private BookService $service;

    protected function setUp(): void
    {
        // Creamos los mocks
        $this->repository = $this->createMock(BookRepository::class);
        $this->apiClient = $this->createMock(BookApiClient::class);

        // Inyectamos los mocks en el servicio
        $this->service = new BookService($this->repository, $this->apiClient);
    }

    public function testCreateBookWithApiData()
    {
        $book = new Book(null, 'El Principito', 'Antoine', '1234567890');

        // Simulamos respuesta de la API
        $this->apiClient->method('fetchBookData')
            ->with('1234567890')
            ->willReturn([
                'description' => 'Un libro muy famoso',
                'coverUrl' => 'https://cover.url/image.jpg'
            ]);

        // Simulamos repositorio
        $this->repository->method('save')
            ->willReturnCallback(fn($b) => $b);

        $savedBook = $this->service->createBook($book);

        $this->assertEquals('Un libro muy famoso', $savedBook->description);
        $this->assertEquals('https://cover.url/image.jpg', $savedBook->coverUrl);
    }
}
