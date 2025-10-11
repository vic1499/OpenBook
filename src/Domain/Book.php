<?php
namespace OpenBook\Domain;

class Book
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $author,
        public ?string $isbn = null,
        public ?int $year = null,
        public ?string $description = null,
        public ?string $coverUrl = null
    ) {}
}
