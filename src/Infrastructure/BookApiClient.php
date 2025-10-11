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

            // 1️⃣ Llamada principal (api/books)
            $response = $this->httpClient->get('api/books', [
                'query' => [
                    'bibkeys' => "ISBN:$isbn",
                    'format' => 'json',
                    'jscmd' => 'data'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $key = "ISBN:$isbn";
            if (!isset($data[$key])) return null;

            $bookInfo = $data[$key];

            // 2️⃣ Descripción: primero api/books
            $description = $bookInfo['description']['value']
                ?? $bookInfo['description']
                ?? $bookInfo['notes']
                ?? null;

            // 3️⃣ Si no hay, fallback a /isbn/{isbn}.json
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

            // 4️⃣ Cover
            $coverUrl = $bookInfo['cover']['medium'] ?? null;

            // 5️⃣ Autores
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
}
