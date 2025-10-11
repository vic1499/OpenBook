<?php
require __DIR__ . '/../vendor/autoload.php';

use OpenBook\Application\BookService;
use OpenBook\Infrastructure\BookRepository;
use OpenBook\Infrastructure\BookApiClient;
use OpenBook\Application\AuthService;
use OpenBook\Infrastructure\UserRepository;

$authRepo = new UserRepository();
$authService = new AuthService($authRepo);


session_name('openbook_sess');
session_start();


// Crear dependencias
$repository = new BookRepository();
$apiClient = new BookApiClient();
$service = new BookService($repository, $apiClient);


// Instanciar el servicio
$service = new BookService($repository, $apiClient);

// Capturar método y ruta
$uri = str_replace('/api.php', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$query = $_GET['q'] ?? null;



// Respuesta JSON
header('Content-Type: application/json');



// ------------------ ENDPOINTS ------------------ //

// Listar todos los libros
if ($uri === '/books' && $method === 'GET') {
    echo json_encode($service->listBooks());
    exit;
}





// Registro
if ($uri === '/register' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $result = $authService->register(
        $data['name'] ?? '',
        $data['email'] ?? '',
        $data['password'] ?? ''
    );
    echo json_encode($result);
    exit;
}

// Login
if ($uri === '/login' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $result = $authService->login($data['email'] ?? '', $data['password'] ?? '');

    if (!empty($result['success']) && isset($result['user'])) {
        // Devuelve el usuario correcto desde la BD
        echo json_encode([
            'success' => true,
            'message' => 'Inicio de sesión correcto',
            'user' => [
                'id' => $result['user']['id'],
                'name' => $result['user']['name']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $result['error'] ?? 'Credenciales incorrectas'
        ]);
    }
    exit;
}



// Logout
if ($uri === '/logout' && $method === 'POST') {
    $authService->logout();
    echo json_encode(['success' => true, 'message' => 'Sesión cerrada']);
    exit;
}



// Buscar libros
if ($uri === '/books/search' && $method === 'GET' && $query) {
    $books = $service->searchBooks($query);
    //var_dump($books); // DEBUG
    echo json_encode($books);
    exit;
}


// Crear un libro
if ($uri === '/books/create' && $method === 'POST') {
    //validateCsrf(); // Validamos CSRF antes de crear
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['title']) || empty($data['author']) || empty($data['isbn'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields: title, author, isbn']);
        exit;
    }

    $book = $service->createBook(new \OpenBook\Domain\Book(
        id: null,
        title: $data['title'],
        author: $data['author'],
        isbn: $data['isbn']
    ));

    echo json_encode($book);
    exit;
}

// Actualizar un libro
if ($uri === '/books/update' && $method === 'POST') {
      //validateCsrf();
        $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing book ID']);
        exit;
    }

    $updatedBook = $service->updateBook($id, $data);
    echo json_encode([
        'success' => (bool) $updatedBook,
        'message' => $updatedBook ? 'Book updated successfully' : 'Book not found'
    ]);
    exit;
}

// Eliminar un libro
if ($uri === '/books/delete' && in_array($method, ['DELETE', 'POST'])) {
    //validateCsrf(); //  Validamos CSRF antes de eliminar
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing book ID']);
        exit;
    }

    $deleted = $service->deleteBook($id);
    echo json_encode([
        'success' => $deleted,
        'message' => $deleted ? 'Book deleted successfully' : 'Book not found'
    ]);
    exit;
}


// Obtener libro por ID
if (preg_match('#^/books/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $bookId = (int) $matches[1];
    $book = $service->getBookById($bookId);
    echo json_encode($book);
    exit;
}


// Endpoint no encontrado
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
