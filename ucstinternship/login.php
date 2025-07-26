<?php
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Form</title>
</head>
<body>
    <h1>Admin Login</h1>
    <form id="login-form">
        <input type="email" id="email" placeholder="Email" required><br><br>
        <input type="password" id="password" placeholder="Password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <p id="msg" style="color:red;"></p>

    <script>
        document.getElementById('login-form').addEventListener('submit', async e => {
            e.preventDefault();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            const res = await fetch('backend.php?action=login', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({email, password})
            });
            const data = await res.json();
            if (data.success) {
                window.location.href = 'dashboard.php';
            } else {
                document.getElementById('msg').textContent = data.error || 'Login failed';
            }
        });
    </script>
</body>
</html>
