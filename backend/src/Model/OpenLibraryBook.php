<?php
// C:\Code\work-sample-master\backend\src\Model\OpenLibraryBook.php
namespace App\Model;

class OpenLibraryBook implements \JsonSerializable
{
    private array $identifiers;
    private string $title;
    private array $authors;
    private string $publish_date;  // Changed from array to string
    private array $isbn_13;  
    

    public function __construct(array $data) {
        $this->identifiers = $data['identifiers'] ?? [];
        $this->title = $data['title'] ?? '';
        $this->authors = $data['authors'] ?? [];
        $this->publish_date = $data['publish_date'] ?? ''; // Default to an empty string if not provided
        $this->isbn_13 = $data['isbn_13'] ?? []; // Default to an empty string if not provided
        // ... set other properties
    }

    public function getIdentifiers(): array {
        return $this->identifiers;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getAuthors(): array {
        return $this->authors;
    }

    public function getPublishDate(): string {
        return $this->publish_date;
    }

    public function getIsbn_13(): array {
        return $this->isbn_13;
    }

    // ... other getters

    public function jsonSerialize()
    {
        return [
            'identifiers' => $this->getIdentifiers(),
            'title' => $this->getTitle(),
            'authors' => $this->getAuthors(),
            'publish_date' => $this->getPublishDate(), // Corrected method name here as well
            'isbn_13' => $this->getIsbn_13(), // Corrected method name here as well
            // ... other properties you want to include
        ];
    }
}
