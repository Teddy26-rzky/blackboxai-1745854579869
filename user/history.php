<?php
// user/history.php - Halaman histori penyewaan user

session_start();
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data booking user dengan status pembayaran dan booking
$stmt = $pdo->prepare('
    SELECT b.*, r.nama_room, r.gambar
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
');
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Histori Penyewaan - Sewa Room Apartemen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Histori Penyewaan</h1>
            <nav>
                <a href="../index.php" class="mr-4 hover:underline">Beranda</a>
                <a href="../logout.php" class="hover:underline">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-4">
        <?php if (empty($bookings)): ?>
            <p>Anda belum melakukan penyewaan.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($bookings as $booking): ?>
                    <div class="bg-white rounded shadow p-4">
                        <?php if ($booking['gambar']): ?>
                            <img src="../images/<?= htmlspecialchars($booking['gambar']) ?>" alt="<?= htmlspecialchars($booking['nama_room']) ?>" class="w-full h-48 object-cover rounded mb-4" />
                        <?php endif; ?>
                        <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($booking['nama_room']) ?></h3>
                        <p>Tanggal Mulai: <?= htmlspecialchars($booking['tanggal_mulai']) ?></p>
                        <p>Tanggal Selesai: <?= htmlspecialchars($booking['tanggal_selesai']) ?></p>
                        <p>Status Booking: <?= htmlspecialchars($booking['status_booking']) ?></p>
                        <p>Status Pembayaran: <?= htmlspecialchars($booking['status_pembayaran']) ?></p>
                        <a href="../invoice.php?booking_id=<?= $booking['id'] ?>" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Lihat Invoice</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
