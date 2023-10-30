<?php

namespace App\Service;

use App\Model\OpenLibraryBook;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenLibraryClient 
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client) {
        $this->client = $client;
    }

    public function getBookByIsbn(string $isbn) : ?OpenLibraryBook
    {
        $response = $this->client->request('GET', 'https://openlibrary.org/isbn/'.$isbn.'.json');
    
        if ($response->getStatusCode() !== 200) {
             return null;
        }
    
        $data = $response->toArray();
       
    
        return new OpenLibraryBook($data); 
    }
}    
