<?php
// invoice.php - Halaman invoice untuk booking tertentu

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

// Ambil data booking, user, room, dan pembayaran
$stmt = $pdo->prepare('
    SELECT b.*, u.nama AS user_nama, u.nomor_telepon, u.alamat, r.nama_room, r.harga, r.gambar,
    (SELECT status FROM payments WHERE booking_id = b.id ORDER BY tanggal_pembayaran DESC LIMIT 1) AS payment_status,
    (SELECT metode FROM payments WHERE booking_id = b.id ORDER BY tanggal_pembayaran DESC LIMIT 1) AS payment_method
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN rooms r ON b.room_id = r.id
    WHERE b.id = ? AND b.user_id = ?
');
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    echo "Invoice tidak ditemukan.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Invoice Booking #<?= $booking['id'] ?> - Sewa Room Apartemen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-lg">
        <h1 class="text-3xl font-bold mb-6 text-center">Invoice Booking #<?= $booking['id'] ?></h1>

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Detail Penyewa</h2>
            <p>Nama: <?= htmlspecialchars($booking['user_nama']) ?></p>
            <p>Nomor Telepon: <?= htmlspecialchars($booking['nomor_telepon']) ?></p>
            <p>Alamat: <?= nl2br(htmlspecialchars($booking['alamat'])) ?></p>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Detail Room</h2>
            <?php if ($booking['gambar']): ?>
                <img src="images/<?= htmlspecialchars($booking['gambar']) ?>" alt="<?= htmlspecialchars($booking['nama_room']) ?>" class="w-full h-48 object-cover rounded mb-4" />
            <?php endif; ?>
            <p>Room: <?= htmlspecialchars($booking['nama_room']) ?></p>
            <p>Harga per periode: Rp <?= number_format($booking['harga'], 0, ',', '.') ?></p>
            <p>Tanggal Mulai: <?= htmlspecialchars($booking['tanggal_mulai']) ?></p>
            <p>Tanggal Selesai: <?= htmlspecialchars($booking['tanggal_selesai']) ?></p>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Pembayaran</h2>
            <p>Metode Pembayaran: <?= htmlspecialchars($booking['payment_method'] ?? $booking['metode_pembayaran']) ?></p>
            <p>Status Pembayaran: <?= htmlspecialchars($booking['payment_status'] ?? $booking['status_pembayaran']) ?></p>
        </div>

        <div class="text-center">
            <a href="index.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
