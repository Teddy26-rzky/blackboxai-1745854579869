<?php
// login.php - Form login user dan proses autentikasi

session_start();
require 'config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }
    if (!$password) {
        $errors[] = 'Password harus diisi.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login berhasil
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Email atau password salah.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Sewa Room Apartemen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>

        <?php if (isset($_GET['registered'])): ?>
            <div class="mb-4 bg-green-100 text-green-700 p-3 rounded">
                Registrasi berhasil. Silakan login.
            </div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <label class="block mb-2 font-semibold" for="email">Email</label>
            <input class="w-full p-2 mb-4 border rounded" type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required />

            <label class="block mb-6 font-semibold" for="password">Password</label>
            <input class="w-full p-2 mb-6 border rounded" type="password" id="password" name="password" required />

            <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition" type="submit">Login</button>
        </form>

        <p class="mt-4 text-center">
            Belum punya akun? <a href="register.php" class="text-blue-600 hover:underline">Daftar di sini</a>
        </p>
    </div>
</body>
</html>
