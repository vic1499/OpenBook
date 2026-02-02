<?php
require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use OpenBook\Infrastructure\BookRepository;
use OpenBook\Infrastructure\BookApiClient;

session_name('openbook_sess');
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Acceso denegado');
}

$id = $_GET['id'] ?? null;
if (!$id)
    die('Falta ID de libro');

$repo = new BookRepository();
$api = new BookApiClient();

$bookData = $repo->getBookById((int) $id);
if (!$bookData)
    die('Libro no encontrado');

// Intentar obtener info extra de Google Books
$googleData = $api->fetchDetailedGoogleBooksData($bookData['isbn']);

$title = $googleData['title'] ?? $bookData['title'];
$author = isset($googleData['authors']) ? implode(', ', $googleData['authors']) : $bookData['author'];
$isbn = $bookData['isbn'];
$date = $googleData['publishedDate'] ?? 'N/A';
$description = $googleData['description'] ?? $bookData['description'] ?? 'Sin descripción disponible.';
$coverUrl = $googleData['coverUrl'] ?? $bookData['coverUrl'] ?? '';

// Convertir imagen a base64 para evitar errores de dompdf con URLs remotas
$base64Image = null;
if ($coverUrl) {
    try {
        $client = new \GuzzleHttp\Client(['timeout' => 5.0, 'verify' => false]);
        $imageResponse = $client->get($coverUrl);
        if ($imageResponse->getStatusCode() === 200) {
            $imageData = $imageResponse->getBody()->getContents();
            $imageType = $imageResponse->getHeaderLine('Content-Type');
            $base64Image = 'data:' . $imageType . ';base64,' . base64_encode($imageData);
        }
    } catch (\Exception $e) {
        // Fallback to null if image fetch fails
    }
}

// Configurar Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);

$html = "
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #405189; padding-bottom: 10px; margin-bottom: 20px; }
        .title { color: #405189; font-size: 24px; margin-bottom: 5px; }
        .author { font-size: 18px; color: #666; }
        .content { display: block; }
        .cover { float: left; margin-right: 20px; border: 1px solid #ddd; padding: 5px; max-width: 150px; }
        .info { overflow: hidden; }
        .info p { margin: 5px 0; }
        .synopsis { margin-top: 30px; clear: both; }
        .synopsis h3 { border-bottom: 1px solid #eee; padding-bottom: 5px; color: #405189; }
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; height: 50px; text-align: center; font-size: 10px; color: #aaa; }
    </style>
</head>
<body>
    <div class='header'>
        <div class='title'>$title</div>
        <div class='author'>$author</div>
    </div>

    <div class='content'>
        " . ($base64Image ? "<img src='$base64Image' class='cover'>" : "") . "
        <div class='info'>
            <p><strong>ISBN:</strong> $isbn</p>
            <p><strong>Fecha de Publicación:</strong> $date</p>
        </div>
    </div>

    <div class='synopsis'>
        <h3>Sinopsis Completa</h3>
        <p>$description</p>
    </div>

    <div class='footer'>
        Generado por OpenBook Dashboard - " . date('d/m/Y H:i') . "
    </div>
</body>
</html>
";

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("Sinopsis_$isbn.pdf", ["Attachment" => false]);
