<?php
// admin/manage_schedule.php - Halaman untuk mengatur jadwal penyewaan (ubah status booking)

session_start();
require '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Proses update status booking
if (isset($_POST['booking_id'], $_POST['status_booking'])) {
    $booking_id = (int)$_POST['booking_id'];
    $status_booking = $_POST['status_booking'];

    if (in_array($status_booking, ['pending', 'confirmed', 'cancelled'])) {
        $stmt = $pdo->prepare('UPDATE bookings SET status_booking = ? WHERE id = ?');
        $stmt->execute([$status_booking, $booking_id]);
    }
}

// Ambil data booking
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
    <title>Atur Jadwal - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Atur Jadwal Penyewaan</h1>
            <nav>
                <a href="dashboard.php" class="mr-4 hover:underline">Dashboard</a>
                <a href="confirm_payment.php" class="mr-4 hover:underline">Konfirmasi Pembayaran</a>
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
                        <th class="py-2 px-4">Aksi</th>
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
                            <td class="py-2 px-4">
                                <form method="POST" action="manage_schedule.php">
                                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>" />
                                    <select name="status_booking" class="border rounded p-1">
                                        <option value="pending" <?= $booking['status_booking'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="confirmed" <?= $booking['status_booking'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="cancelled" <?= $booking['status_booking'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition ml-2">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
