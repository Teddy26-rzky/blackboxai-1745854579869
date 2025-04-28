<?php
// booking.php - Form booking room dan proses booking

session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$room_id = $_GET['room_id'] ?? null;
if (!$room_id) {
    header('Location: index.php');
    exit;
}

// Ambil data room
$stmt = $pdo->prepare('SELECT * FROM rooms WHERE id = ? AND status = "tersedia"');
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    echo "Room tidak tersedia.";
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $metode_pembayaran = $_POST['metode_pembayaran'] ?? '';

    if (!$tanggal_mulai) {
        $errors[] = 'Tanggal mulai harus diisi.';
    }
    if (!$tanggal_selesai) {
        $errors[] = 'Tanggal selesai harus diisi.';
    }
    if (!$metode_pembayaran || !in_array($metode_pembayaran, ['COD', 'transfer'])) {
        $errors[] = 'Metode pembayaran tidak valid.';
    }
    if (strtotime($tanggal_selesai) < strtotime($tanggal_mulai)) {
        $errors[] = 'Tanggal selesai harus setelah tanggal mulai.';
    }

    if (empty($errors)) {
        // Simpan booking dengan status pending
        $stmt = $pdo->prepare('INSERT INTO bookings (user_id, room_id, tanggal_mulai, tanggal_selesai, metode_pembayaran) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $room_id, $tanggal_mulai, $tanggal_selesai, $metode_pembayaran]);
        $booking_id = $pdo->lastInsertId();

        header("Location: payment.php?booking_id=$booking_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Booking Room - Sewa Room Apartemen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Booking <?= htmlspecialchars($room['nama_room']) ?></h1>

        <?php if ($errors): ?>
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="booking.php?room_id=<?= $room_id ?>" novalidate>
            <label class="block mb-2 font-semibold" for="tanggal_mulai">Tanggal Mulai</label>
            <input class="w-full p-2 mb-4 border rounded" type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?= htmlspecialchars($_POST['tanggal_mulai'] ?? '') ?>" required />

            <label class="block mb-2 font-semibold" for="tanggal_selesai">Tanggal Selesai</label>
            <input class="w-full p-2 mb-4 border rounded" type="date" id="tanggal_selesai" name="tanggal_selesai" value="<?= htmlspecialchars($_POST['tanggal_selesai'] ?? '') ?>" required />

            <label class="block mb-2 font-semibold" for="metode_pembayaran">Metode Pembayaran</label>
            <select class="w-full p-2 mb-6 border rounded" id="metode_pembayaran" name="metode_pembayaran" required>
                <option value="">Pilih metode pembayaran</option>
                <option value="COD" <?= (($_POST['metode_pembayaran'] ?? '') === 'COD') ? 'selected' : '' ?>>COD</option>
                <option value="transfer" <?= (($_POST['metode_pembayaran'] ?? '') === 'transfer') ? 'selected' : '' ?>>Transfer</option>
            </select>

            <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition" type="submit">Booking Sekarang</button>
        </form>

        <p class="mt-4 text-center">
            <a href="index.php" class="text-blue-600 hover:underline">Kembali ke daftar room</a>
        </p>
    </div>
</body>
</html>
