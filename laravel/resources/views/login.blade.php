<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div class="login-container">
        <div class="icon">👤</div> 
        <h1>Login</h1>
        @if(session('error'))
            <div class="error-message">{{ session('error') }}</div>
        @endif
        <form action="{{ route('login.process') }}" method="POST">
            @csrf
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">LOGIN</button>
            <a href="{{ route('registrasi') }}" class="account">Daftar Akun</a> 
        </form>
    </div>

<script>
    function handleLogin(event) {
        event.preventDefault(); 
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        console.log('Attempting to login with:', { username, password });

        axios.post('http://10.25.200.21:8000/users/login', { username, password }) 
            .then(response => {
                console.log('Login successful:', response.data);
                window.location.href = '/dashboard';
            })
            .catch(error => {
                    console.error('Login failed:', error);
                console.error('Error response:', error.response);
                const errorMessage = document.getElementById('error-message');
                if (error.response) {
                    errorMessage.innerText = error.response.data.message || 'Login failed.';
                } else {
                    errorMessage.innerText = 'An unexpected error occurred. Please try again later.';
                }
                errorMessage.style.display = 'block';
            });
        }
</script>
</body>
</html>