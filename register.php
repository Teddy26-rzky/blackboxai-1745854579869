<?php
// register.php - Form registrasi user dan proses registrasi

require 'config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $nomor_telepon = trim($_POST['nomor_telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$nama) {
        $errors[] = 'Nama harus diisi.';
    }
    if (!$nomor_telepon) {
        $errors[] = 'Nomor telepon harus diisi.';
    }
    if (!$alamat) {
        $errors[] = 'Alamat harus diisi.';
    }
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }
    if (!$password) {
        $errors[] = 'Password harus diisi.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Password dan konfirmasi password tidak sama.';
    }

    if (empty($errors)) {
        // Cek apakah email sudah terdaftar
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email sudah terdaftar.';
        } else {
            // Simpan user baru
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (nama, nomor_telepon, alamat, email, password) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$nama, $nomor_telepon, $alamat, $email, $password_hash]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registrasi - Sewa Room Apartemen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Registrasi Akun</h1>

        <?php if ($errors): ?>
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" novalidate>
            <label class="block mb-2 font-semibold" for="nama">Nama</label>
            <input class="w-full p-2 mb-4 border rounded" type="text" id="nama" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required />

            <label class="block mb-2 font-semibold" for="nomor_telepon">Nomor Telepon</label>
            <input class="w-full p-2 mb-4 border rounded" type="text" id="nomor_telepon" name="nomor_telepon" value="<?= htmlspecialchars($_POST['nomor_telepon'] ?? '') ?>" required />

            <label class="block mb-2 font-semibold" for="alamat">Alamat</label>
            <textarea class="w-full p-2 mb-4 border rounded" id="alamat" name="alamat" required><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>

            <label class="block mb-2 font-semibold" for="email">Email</label>
            <input class="w-full p-2 mb-4 border rounded" type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required />

            <label class="block mb-2 font-semibold" for="password">Password</label>
            <input class="w-full p-2 mb-6 border rounded" type="password" id="password" name="password" required />

            <label class="block mb-2 font-semibold" for="password_confirm">Konfirmasi Password</label>
            <input class="w-full p-2 mb-6 border rounded" type="password" id="password_confirm" name="password_confirm" required />

            <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition" type="submit">Daftar</button>
        </form>

        <p class="mt-4 text-center">
            Sudah punya akun? <a href="login.php" class="text-blue-600 hover:underline">Login di sini</a>
        </p>
    </div>
</body>
</html>
