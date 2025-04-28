<?php
// index.php - Halaman utama menampilkan daftar room

require 'config.php';

$stmt = $pdo->query('SELECT * FROM rooms WHERE status = "tersedia"');
$rooms = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Website Sewa Room Apartemen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Sewa Room Apartemen</h1>
            <nav>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="user/history.php" class="mr-4 hover:underline">Histori Penyewaan</a>
                    <a href="logout.php" class="hover:underline">Logout</a>
                <?php else: ?>
                    <a href="register.php" class="mr-4 hover:underline">Daftar</a>
                    <a href="login.php" class="hover:underline">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-4 flex-grow">
        <h2 class="text-2xl font-semibold mb-4">Room Tersedia</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($rooms as $room): ?>
                        <div class="bg-white rounded shadow p-4">
                            <?php if ($room['gambar']): ?>
                                <img src="images/<?= htmlspecialchars($room['gambar']) ?>" alt="<?= htmlspecialchars($room['nama_room']) ?>" class="w-full h-48 object-cover rounded mb-4" />
                            <?php endif; ?>
                            <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($room['nama_room']) ?></h3>
                            <p class="mb-2"><?= htmlspecialchars($room['deskripsi']) ?></p>
                            <p class="mb-4 font-semibold">Harga: Rp <?= number_format($room['harga'], 0, ',', '.') ?></p>
                            <a href="booking.php?room_id=<?= $room['id'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Booking Sekarang</a>
                        </div>
                    <?php endforeach; ?>
        </div>
    </main>

    <footer class="bg-gray-800 text-white p-4 text-center">
        &copy; <?= date('Y') ?> Sewa Room Apartemen
    </footer>
</body>
</html>
