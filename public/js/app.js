// app.js
const apiUrl = 'api.php/books';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}




// funcion para el buscador 
function renderTable(books) {
    const tbody = document.getElementById('booksList');
    if (!tbody) return;
    tbody.innerHTML = '';

    books.forEach(b => {
        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td><a href="edit.php?id=${b.id}" class="fw-medium link-primary">#${b.id ?? ''}</a></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-2">
                        ${b.coverUrl ? `<img src="${b.coverUrl}" alt="" class="avatar-xs rounded shadow" style="width:32px;height:45px;object-fit:cover;">` : '<div class="avatar-xs bg-light rounded d-flex align-items-center justify-content-center" style="width:32px;height:45px;"><i class="bi bi-book text-muted"></i></div>'}
                    </div>
                </div>
            </td>
            <td>
                <h6 class="fs-14 mb-1">${escapeHtml(b.title)}</h6>
                <p class="text-muted mb-0">${escapeHtml(b.author)}</p>
            </td>
            <td><span class="badge bg-light text-body border-secondary-subtle border">${b.isbn ?? ''}</span></td>
            <td class="text-wrap" style="max-width: 250px;">
                <span class="text-muted text-truncate-two-lines d-block">${escapeHtml(b.description ?? 'No disponible')}</span>
            </td>
            <td>
                <div class="hstack gap-2 mt-auto">
                    <button onclick="editBook(${b.id})" class="btn btn-sm btn-soft-info" title="Editar">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button onclick="deleteBook(${b.id})" class="btn btn-sm btn-soft-danger" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
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


// Crear libro (Null check added because form might not be on this page)
const createForm = document.getElementById('createForm');
if (createForm) {
    createForm.addEventListener('submit', async e => {
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
            window.location.href = 'index.php';
        } catch (err) {
            console.error('Error creando libro:', err);
        }
    });
}

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

