// edit.js
const apiUrl = 'http://localhost:8080/api.php/books';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// Obtener el ID del libro de la URL
const urlParams = new URLSearchParams(window.location.search);
const bookId = urlParams.get('id');

const form = document.getElementById('editForm');
const titleInput = document.getElementById('title');
const authorInput = document.getElementById('author');
const isbnInput = document.getElementById('isbn');
const descriptionInput = document.getElementById('description');
const coverContainer = document.getElementById('coverContainer');

// Función para escapar HTML (XSS)
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Cargar datos del libro
async function loadBook() {
    if (!bookId) return;

    try {
        const res = await fetch(`${apiUrl}/${bookId}`);
        const book = await res.json();

        titleInput.value = book.title ?? '';
        authorInput.value = book.author ?? '';
        isbnInput.value = book.isbn ?? '';
        descriptionInput.value = book.description ?? '';

        coverContainer.innerHTML = book.coverUrl
            ? `<img src="${escapeHtml(book.coverUrl)}" alt="Portada">`
            : '';
    } catch (err) {
        console.error('Error cargando libro:', err);
        alert('No se pudo cargar el libro');
    }
}

// Guardar cambios (update)
form.addEventListener('submit', async e => {
    e.preventDefault();

    const updatedBook = {
        id: bookId,
        title: titleInput.value.trim(),
        author: authorInput.value.trim(),
        isbn: isbnInput.value.trim(),
        description: descriptionInput.value.trim()
    };

    try {
        const res = await fetch(`${apiUrl}/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken // si estás usando CSRF
            },
            body: JSON.stringify(updatedBook)
        });

        const data = await res.json();

        if (data.success) {
            alert('Libro actualizado correctamente!');
            window.location.href = 'index.php'; // Volver al listado
        } else {
            alert(data.message || 'Error al actualizar libro');
        }
    } catch (err) {
        console.error('Error actualizando libro:', err);
        alert('Error al actualizar libro');
    }
});

// Botón Sinapsis PDF
const btnSinapsis = document.getElementById('btnSinapsis');
if (btnSinapsis) {
    btnSinapsis.addEventListener('click', () => {
        window.open('generate-synopsis.php?id=' + bookId, '_blank');
    });
}

// Inicializar
loadBook();
