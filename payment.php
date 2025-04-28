<?php
// payment.php - Halaman pembayaran dan konfirmasi pembayaran user

session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$booking_id = $_GET['booking_id'] ?? null;
if (!$booking_id) {
    header('Location: index.php');
    exit;
}

// Ambil data booking dan room
$stmt = $pdo->prepare('
    SELECT b.*, r.nama_room, r.harga 
    FROM bookings b 
    JOIN rooms r ON b.room_id = r.id 
    WHERE b.id = ? AND b.user_id = ?
');
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    echo "Booking tidak ditemukan.";
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses konfirmasi pembayaran untuk metode transfer
    if ($booking['metode_pembayaran'] === 'transfer') {
        // Simpan data pembayaran dengan status pending
        $stmt = $pdo->prepare('INSERT INTO payments (booking_id, jumlah, metode, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([$booking_id, $booking['harga'], 'transfer', 'pending']);

        // Update status pembayaran booking menjadi pending (jika belum)
        $stmt = $pdo->prepare('UPDATE bookings SET status_pembayaran = ? WHERE id = ?');
        $stmt->execute(['pending', $booking_id]);

        $success = true;
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pembayaran - Sewa Room Apartemen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Pembayaran untuk <?= htmlspecialchars($booking['nama_room']) ?></h1>

        <p class="mb-4">Tanggal Sewa: <?= htmlspecialchars($booking['tanggal_mulai']) ?> sampai <?= htmlspecialchars($booking['tanggal_selesai']) ?></p>
        <p class="mb-4 font-semibold">Total Harga: Rp <?= number_format($booking['harga'], 0, ',', '.') ?></p>
        <p class="mb-6">Metode Pembayaran: <?= htmlspecialchars($booking['metode_pembayaran']) ?></p>

        <?php if ($booking['metode_pembayaran'] === 'COD'): ?>
            <p class="mb-6">Pembayaran akan dilakukan secara COD (Cash on Delivery) saat pengambilan kunci.</p>
            <p class="mb-6 font-semibold text-green-700">Booking Anda sedang diproses. Silakan tunggu konfirmasi dari admin.</p>
            <p class="text-center"><a href="index.php" class="text-blue-600 hover:underline">Kembali ke halaman utama</a></p>
        <?php elseif ($booking['metode_pembayaran'] === 'transfer'): ?>
            <?php if ($success): ?>
                <div class="mb-4 bg-green-100 text-green-700 p-3 rounded">
                    Konfirmasi pembayaran berhasil dikirim. Silakan tunggu konfirmasi dari admin.
                </div>
            <?php else: ?>
                <form method="POST" action="payment.php?booking_id=<?= $booking_id ?>" novalidate>
                    <p class="mb-4">Silakan lakukan transfer ke rekening berikut:</p>
                    <p class="mb-4 font-semibold">Bank ABC - No. Rekening: 1234567890 - a.n. Sewa Room Apartemen</p>
                    <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition" type="submit">Konfirmasi Pembayaran</button>
                </form>
            <?php endif; ?>
            <p class="mt-6 text-center"><a href="index.php" class="text-blue-600 hover:underline">Kembali ke halaman utama</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
