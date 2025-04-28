<?php
// admin/confirm_payment.php - Halaman untuk mengkonfirmasi pembayaran

session_start();
require '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Proses konfirmasi pembayaran
if (isset($_POST['payment_id'], $_POST['action'])) {
    $payment_id = (int)$_POST['payment_id'];
    $action = $_POST['action'];

    if ($action === 'confirm') {
        // Update status pembayaran menjadi confirmed
        $stmt = $pdo->prepare('UPDATE payments SET status = ? WHERE id = ?');
        $stmt->execute(['confirmed', $payment_id]);

        // Update status pembayaran di booking juga
        $stmt = $pdo->prepare('
            UPDATE bookings b
            JOIN payments p ON b.id = p.booking_id
            SET b.status_pembayaran = ?
            WHERE p.id = ?
        ');
        $stmt->execute(['confirmed', $payment_id]);
    }
}

// Ambil data pembayaran yang statusnya pending
$stmt = $pdo->query('
    SELECT p.*, b.id AS booking_id, u.nama AS user_nama, r.nama_room
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    JOIN users u ON b.user_id = u.id
    JOIN rooms r ON b.room_id = r.id
    WHERE p.status = "pending"
    ORDER BY p.tanggal_pembayaran DESC
');
$payments = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Konfirmasi Pembayaran - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Konfirmasi Pembayaran</h1>
            <nav>
                <a href="dashboard.php" class="mr-4 hover:underline">Dashboard</a>
                <a href="manage_schedule.php" class="mr-4 hover:underline">Atur Jadwal</a>
                <a href="../logout.php" class="hover:underline">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Pembayaran Pending</h2>

        <?php if (empty($payments)): ?>
            <p>Tidak ada pembayaran yang perlu dikonfirmasi.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded shadow">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="py-2 px-4">ID Pembayaran</th>
                            <th class="py-2 px-4">User</th>
                            <th class="py-2 px-4">Room</th>
                            <th class="py-2 px-4">Jumlah</th>
                            <th class="py-2 px-4">Metode</th>
                            <th class="py-2 px-4">Tanggal Pembayaran</th>
                            <th class="py-2 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr class="border-t text-center">
                                <td class="py-2 px-4"><?= $payment['id'] ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($payment['user_nama']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($payment['nama_room']) ?></td>
                                <td class="py-2 px-4">Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($payment['metode']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($payment['tanggal_pembayaran']) ?></td>
                                <td class="py-2 px-4">
                                    <form method="POST" action="confirm_payment.php" onsubmit="return confirm('Konfirmasi pembayaran ini?');">
                                        <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>" />
                                        <button type="submit" name="action" value="confirm" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition">Konfirmasi</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
