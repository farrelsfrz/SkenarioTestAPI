<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="icon">📝</div> 
        <h1>Registrasi</h1>
        <form action="{{ route('register.user') }}" method="POST" onsubmit="registerUser(event)">
            @csrf
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <div>
                <label for="role">Pilih Role:</label>
                <select name="role" id="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit">DAFTAR</button>
            <a href="{{ route('login') }}" class="account">Sudah punya akun? Masuk</a>
        </form>
    </div>

    <script>
        async function registerUser(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    alert(errorData.errors.map(err => err.msg).join(', '));
                    return;
                }

                alert('Pendaftaran berhasil!');
                window.location.href = '{{ route('login') }}'; 
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal mendaftar, silakan coba lagi.');
            }
        }
    </script>
</body>
</html>