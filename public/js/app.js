// app.js
const apiUrl = 'api.php/books';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

let currentPage = 1;
const recordsPerPage = 5;

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Renderizar la tabla de libros
function renderTable(books) {
    const tbody = document.getElementById('booksList');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (books.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron libros.</td></tr>';
        return;
    }

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
                <span class="text-muted text-truncate-two-lines d-block">${escapeHtml(b.description)}</span>
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

// Generar controles de paginación
function renderPagination(totalPages, currentPage, totalCount) {
    const container = document.getElementById('pagination-container');
    const totalLabel = document.getElementById('pagination-total');
    const startLabel = document.getElementById('pagination-start');

    if (!container) return;
    container.innerHTML = '';

    if (totalLabel) totalLabel.innerText = totalCount;
    if (startLabel) startLabel.innerText = Math.min(totalCount, (currentPage - 1) * recordsPerPage + 1) + '-' + Math.min(totalCount, currentPage * recordsPerPage);

    // Botón Anterior
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a class="page-link" href="javascript:void(0);" onclick="changePage(${currentPage - 1})">Anterior</a>`;
    container.appendChild(prevLi);

    // Páginas numéricas
    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="javascript:void(0);" onclick="changePage(${i})">${i}</a>`;
        container.appendChild(li);
    }

    // Botón Siguiente
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a class="page-link" href="javascript:void(0);" onclick="changePage(${currentPage + 1})">Siguiente</a>`;
    container.appendChild(nextLi);
}

function changePage(page) {
    currentPage = page;
    loadBooks();
}

// Cargar libros paginados
async function loadBooks() {
    try {
        const res = await fetch(`${apiUrl}?page=${currentPage}&limit=${recordsPerPage}`);
        const data = await res.json();
        renderTable(data.books);
        renderPagination(data.totalPages, data.currentPage, data.totalCount);
    } catch (err) {
        console.error('Error cargando libros:', err);
    }
}

// Búsqueda en tiempo real (Simplificada para resetear a p1)
async function searchLive() {
    const term = document.getElementById('searchTerm').value.trim();
    if (!term) {
        currentPage = 1;
        loadBooks();
        return;
    }

    try {
        const res = await fetch(`${apiUrl}/search?q=${encodeURIComponent(term)}`);
        const books = await res.json();
        renderTable(books);
        // Deshabilitar paginación durante la búsqueda simple
        const container = document.getElementById('pagination-container');
        if (container) container.innerHTML = '';
        const startLabel = document.getElementById('pagination-start');
        if (startLabel) startLabel.innerText = books.length;
    } catch (err) {
        console.error('Error buscando libros:', err);
    }
}

// Crear libro
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
            alert(data.message ?? 'Libro guardado!');
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

function editBook(id) {
    window.location.href = `edit.php?id=${id}`;
}

// Inicializar
loadBooks();
