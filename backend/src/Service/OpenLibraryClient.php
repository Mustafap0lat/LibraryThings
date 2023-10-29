<?php
// C:\Code\work-sample-master\backend\src\Service\OpenLibraryClient.php

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
            // Temporarily output the status code and content for debugging
            //var_dump($response->getStatusCode());
            //var_dump($response->getContent());
            //die; // halt execution after dumping
             return null;
        }
    
        $data = $response->toArray();
       
    
        return new OpenLibraryBook($data); 
    }
}    
