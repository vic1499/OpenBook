<?php
namespace OpenBook\Infrastructure;

use GuzzleHttp\Client;

class BookApiClient
{
    private Client $httpClient;
    private string $rateLimitFile;
    private int $maxRequests = 5;
    private int $timeWindow = 10; // segundos

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://openlibrary.org/',
            'timeout' => 5.0,
        ]);

        $this->rateLimitFile = sys_get_temp_dir() . '/openlibrary_rate_limit.json';
        if (!file_exists($this->rateLimitFile)) {
            file_put_contents($this->rateLimitFile, json_encode([]));
        }
    }

    private function checkRateLimit(): void
    {
        $calls = json_decode(file_get_contents($this->rateLimitFile), true);
        $calls = array_filter($calls, fn($t) => $t > time() - $this->timeWindow);

        if (count($calls) >= $this->maxRequests) {
            throw new \Exception('Demasiadas peticiones a la API, espera unos segundos.');
        }

        $calls[] = time();
        file_put_contents($this->rateLimitFile, json_encode($calls));
    }

    public function fetchBookData(string $isbn): ?array
    {
        try {
            $this->checkRateLimit();

            // Llamada principal (api/books)
            $response = $this->httpClient->get('api/books', [
                'query' => [
                    'bibkeys' => "ISBN:$isbn",
                    'format' => 'json',
                    'jscmd' => 'data'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $key = "ISBN:$isbn";
            if (!isset($data[$key]))
                return null;

            $bookInfo = $data[$key];

            // Descripción: primero api/books
            $description = $bookInfo['description']['value']
                ?? $bookInfo['description']
                ?? $bookInfo['notes']
                ?? null;

            //  Si no hay, fallback a /isbn/{isbn}.json
            if (!$description) {
                $editionResponse = $this->httpClient->get("isbn/{$isbn}.json");
                $editionData = json_decode($editionResponse->getBody()->getContents(), true);

                if (isset($editionData['description'])) {
                    $description = is_array($editionData['description'])
                        ? ($editionData['description']['value'] ?? null)
                        : $editionData['description'];
                } elseif (!empty($editionData['works'][0]['key'])) {
                    $workKey = ltrim($editionData['works'][0]['key'], '/');
                    $workResponse = $this->httpClient->get($workKey . '.json');
                    $workData = json_decode($workResponse->getBody()->getContents(), true);

                    if (isset($workData['description'])) {
                        $description = is_array($workData['description'])
                            ? ($workData['description']['value'] ?? null)
                            : $workData['description'];
                    }
                }
            }


            $coverUrl = $bookInfo['cover']['medium'] ?? null;


            $authors = array_map(fn($a) => $a['name'], $bookInfo['authors'] ?? []);

            return [
                'description' => $description,
                'coverUrl' => $coverUrl,
                'title' => $bookInfo['title'] ?? null,
                'authors' => $authors
            ];

        } catch (\Exception $e) {
            return null;
        }
    }
    public function fetchDetailedGoogleBooksData(string $isbn): ?array
    {
        try {
            $client = new Client(['timeout' => 5.0]);
            $response = $client->get('https://www.googleapis.com/books/v1/volumes', [
                'query' => ['q' => "isbn:$isbn"]
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['items']))
                return null;

            $volumeInfo = $data['items'][0]['volumeInfo'];
            return [
                'description' => $volumeInfo['description'] ?? 'No hay sinopsis disponible.',
                'publishedDate' => $volumeInfo['publishedDate'] ?? 'Desconocida',
                'coverUrl' => ($volumeInfo['imageLinks']['thumbnail'] ?? null),
                'title' => $volumeInfo['title'] ?? null,
                'authors' => $volumeInfo['authors'] ?? []
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
