<?php
// admin/rooms.php - Halaman manajemen room (list, tambah, edit, hapus)

session_start();
require '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Proses hapus room
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare('DELETE FROM rooms WHERE id = ?');
    $stmt->execute([$delete_id]);
    header('Location: rooms.php');
    exit;
}

// Proses tambah atau edit room
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nama_room = trim($_POST['nama_room'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = $_POST['harga'] ?? '';
    $gambar = trim($_POST['gambar'] ?? '');

    if (!$nama_room) {
        $errors[] = 'Nama room harus diisi.';
    }
    if (!$harga || !is_numeric($harga)) {
        $errors[] = 'Harga harus berupa angka.';
    }

    if (empty($errors)) {
        if ($id) {
            // Update room
            $stmt = $pdo->prepare('UPDATE rooms SET nama_room = ?, deskripsi = ?, harga = ?, gambar = ? WHERE id = ?');
            $stmt->execute([$nama_room, $deskripsi, $harga, $gambar, $id]);
        } else {
            // Insert room baru
            $stmt = $pdo->prepare('INSERT INTO rooms (nama_room, deskripsi, harga, gambar) VALUES (?, ?, ?, ?)');
            $stmt->execute([$nama_room, $deskripsi, $harga, $gambar]);
        }
        header('Location: rooms.php');
        exit;
    }
}

// Jika edit, ambil data room
$edit_room = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $stmt = $pdo->prepare('SELECT * FROM rooms WHERE id = ?');
    $stmt->execute([$edit_id]);
    $edit_room = $stmt->fetch();
}

// Ambil semua room
$stmt = $pdo->query('SELECT * FROM rooms ORDER BY id DESC');
$rooms = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen Room - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Manajemen Room</h1>
            <nav>
                <a href="dashboard.php" class="mr-4 hover:underline">Dashboard</a>
                <a href="confirm_payment.php" class="mr-4 hover:underline">Konfirmasi Pembayaran</a>
                <a href="manage_schedule.php" class="mr-4 hover:underline">Atur Jadwal</a>
                <a href="../logout.php" class="hover:underline">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4"><?= $edit_room ? 'Edit Room' : 'Tambah Room Baru' ?></h2>

        <?php if ($errors): ?>
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="rooms.php" novalidate>
            <input type="hidden" name="id" value="<?= $edit_room['id'] ?? '' ?>" />
            <label class="block mb-2 font-semibold" for="nama_room">Nama Room</label>
            <input class="w-full p-2 mb-4 border rounded" type="text" id="nama_room" name="nama_room" value="<?= htmlspecialchars($edit_room['nama_room'] ?? '') ?>" required />

            <label class="block mb-2 font-semibold" for="deskripsi">Deskripsi</label>
            <textarea class="w-full p-2 mb-4 border rounded" id="deskripsi" name="deskripsi"><?= htmlspecialchars($edit_room['deskripsi'] ?? '') ?></textarea>

            <label class="block mb-2 font-semibold" for="harga">Harga</label>
            <input class="w-full p-2 mb-4 border rounded" type="number" id="harga" name="harga" value="<?= htmlspecialchars($edit_room['harga'] ?? '') ?>" required />

            <label class="block mb-2 font-semibold" for="gambar">Nama File Gambar</label>
            <input class="w-full p-2 mb-6 border rounded" type="text" id="gambar" name="gambar" value="<?= htmlspecialchars($edit_room['gambar'] ?? '') ?>" placeholder="contoh: room1.jpg" />

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition" type="submit"><?= $edit_room ? 'Update' : 'Tambah' ?></button>
            <?php if ($edit_room): ?>
                <a href="rooms.php" class="ml-4 text-gray-700 hover:underline">Batal</a>
            <?php endif; ?>
        </form>

        <h2 class="text-2xl font-semibold mt-10 mb-4">Daftar Room</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="py-2 px-4">ID</th>
                        <th class="py-2 px-4">Nama Room</th>
                        <th class="py-2 px-4">Deskripsi</th>
                        <th class="py-2 px-4">Harga</th>
                        <th class="py-2 px-4">Gambar</th>
                        <th class="py-2 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): ?>
                        <tr class="border-t text-center">
                            <td class="py-2 px-4"><?= $room['id'] ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($room['nama_room']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($room['deskripsi']) ?></td>
                            <td class="py-2 px-4">Rp <?= number_format($room['harga'], 0, ',', '.') ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($room['gambar']) ?></td>
                            <td class="py-2 px-4">
                                <a href="rooms.php?edit_id=<?= $room['id'] ?>" class="text-blue-600 hover:underline mr-2">Edit</a>
                                <a href="rooms.php?delete_id=<?= $room['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Hapus room ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
