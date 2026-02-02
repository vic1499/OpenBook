<?php
namespace OpenBook\Infrastructure;


use OpenBook\Domain\Book;
use OpenBook\Infrastructure\Database\Connection;
use PDO;

class BookRepository
{
    private PDO $pdo;

    public function __construct()
    {

        $this->pdo = Connection::getInstance();
        $this->init();
    }

    private function init(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS books (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT,
                author TEXT,
                isbn TEXT UNIQUE,
                description TEXT,
                coverUrl TEXT
            )
        ");
    }

    public function save(Book $book): Book
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO books (title, author, isbn, description, coverUrl)
            VALUES (:title, :author, :isbn, :description, :coverUrl)
        ");
        $stmt->execute([
            ':title' => $book->title,
            ':author' => $book->author,
            ':isbn' => $book->isbn,
            ':description' => $book->description,
            ':coverUrl' => $book->coverUrl
        ]);

        $book->id = (int) $this->pdo->lastInsertId();
        return $book;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM books");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => new Book(...$r), $rows);
    }

    public function findById(int $id): ?Book
    {
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Book(...$row) : null;
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        if (empty($fields))
            return false;

        $sql = "UPDATE books SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getBookById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        return $book ?: null;
    }

    public function search(string $term): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE title LIKE :term OR author LIKE :term OR isbn LIKE :term");
        $stmt->execute(['term' => "%$term%"]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new Book(...$r), $rows);
    }
}
