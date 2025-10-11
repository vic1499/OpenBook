// app.js
const apiUrl = 'http://localhost:8080/api.php/books';

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}




// funcion para el buscador 
function renderTable(books) {
    const tbody = document.getElementById('booksList');
    tbody.innerHTML = ''; // Limpiar tabla antes de renderizar

    books.forEach(b => {
        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td>${b.id ?? ''}</td>
            <td>${escapeHtml(b.title)}</td>
            <td>${escapeHtml(b.author)}</td>
            <td>${escapeHtml(b.description ?? 'No disponible')}</td>
            <td>${b.isbn ?? ''}</td>
            <td>${b.coverUrl ? `<img src="${b.coverUrl}" alt="Portada" style="width:40px;height:60px;">` : ''}</td>
            <td>
                <button onclick="editBook(${b.id})" class="update-btn">Modificar</button>
                <button onclick="deleteBook(${b.id})" class="delete-btn">Eliminar</button>
            </td>
        `;

        tbody.appendChild(tr);
    });
}


// Cargar todos los libros
async function loadBooks() {
    try {
        const res = await fetch(`${apiUrl}`);
        const books = await res.json();
        renderTable(books);
    } catch (err) {
        console.error('Error cargando libros:', err);
    }
}

// Búsqueda en tiempo real
async function searchLive() {
    const term = document.getElementById('searchTerm').value.trim();
    if (!term) {
        loadBooks(); // Muestra todos si no hay búsqueda
        return;
    }

    try {
        const res = await fetch(`${apiUrl}/search?q=${encodeURIComponent(term)}`);
        const books = await res.json();
        renderTable(books); // Actualiza tabla correctamente
    } catch (err) {
        console.error('Error buscando libros:', err);
    }
}


// Crear libro
document.getElementById('createForm').addEventListener('submit', async e => {
    e.preventDefault();
    const book = {
        title: document.getElementById('title').value,
        author: document.getElementById('author').value,
        isbn: document.getElementById('isbn').value
    };
    try {
        const res = await fetch(`${apiUrl}/create`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(book)
        });
        const data = await res.json();
        alert(data.message ?? 'Libro guardado !');
        loadBooks();
        e.target.reset();
    } catch (err) {
        console.error('Error creando libro:', err);
    }
});

// Eliminar libro
async function deleteBook(id) {
    if (!confirm('¿Eliminar este libro?')) return;
    try {
        const res = await fetch(`${apiUrl}/delete`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ id })
        });
        const data = await res.json();
        alert(data.message ?? 'Libro eliminado!');
        loadBooks();
    } catch (err) {
        console.error('Error eliminando libro:', err);
    }
}

// Editar libro (lleva a otra página)
function editBook(id) {
    window.location.href = `edit.php?id=${id}`;
}




// Inicializamos la tabla
loadBooks();

