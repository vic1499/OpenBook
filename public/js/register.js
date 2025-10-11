document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const data = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        _csrf: document.getElementById('csrf').value
    };

    const res = await fetch('/api.php/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.getElementById('csrf').value
        },
        body: JSON.stringify(data)
    });

    const result = await res.json();
    const div = document.getElementById('result');

    if (result.success) {
        div.innerHTML = `<div class="alert alert-success">${result.message}</div>
        <a href="/login.php" class="btn btn-primary mt-2 w-100">Ir al login</a>
        `;

    } else {
        div.innerHTML = `<div class="alert alert-danger">${result.error || 'Error al registrar'}</div>`;
    }
});