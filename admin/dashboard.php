<?php
// admin/dashboard.php - Dashboard admin untuk melihat booking dan status pembayaran

session_start();
require '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Ambil data booking dengan user dan room
$stmt = $pdo->query('
    SELECT b.*, u.nama AS user_nama, r.nama_room 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN rooms r ON b.room_id = r.id
    ORDER BY b.created_at DESC
');
$bookings = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Admin - Sewa Room Apartemen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Dashboard Admin</h1>
            <nav>
                <a href="confirm_payment.php" class="mr-4 hover:underline">Konfirmasi Pembayaran</a>
                <a href="manage_schedule.php" class="mr-4 hover:underline">Atur Jadwal</a>
                <a href="../logout.php" class="hover:underline">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Daftar Booking</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="py-2 px-4">ID Booking</th>
                        <th class="py-2 px-4">User</th>
                        <th class="py-2 px-4">Room</th>
                        <th class="py-2 px-4">Tanggal Mulai</th>
                        <th class="py-2 px-4">Tanggal Selesai</th>
                        <th class="py-2 px-4">Status Booking</th>
                        <th class="py-2 px-4">Metode Pembayaran</th>
                        <th class="py-2 px-4">Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr class="border-t text-center">
                            <td class="py-2 px-4"><?= $booking['id'] ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($booking['user_nama']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($booking['nama_room']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($booking['tanggal_mulai']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($booking['tanggal_selesai']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($booking['status_booking']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($booking['metode_pembayaran']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($booking['status_pembayaran']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
