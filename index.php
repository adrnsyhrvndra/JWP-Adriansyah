<?php

    session_start();

    // Inisialisasi data seed jika belum ada dan baru menjalankan aplikasi pertama kali
    if (!isset($_SESSION['tasks'])) {
        $_SESSION['tasks'] = [
            ["id" => 1, "title" => "Belajar PHP", "status" => "belum"],
            ["id" => 2, "title" => "Kerjakan Tugas UX", "status" => "selesai"],
        ];
    }

    // Logika Tambah tugas
    if (isset($_POST['tambah'])) {
        $judul = trim($_POST['tugas']);
        if ($judul !== "") {
            if (!empty($_SESSION['tasks'])) {
                $lastId = max(array_column($_SESSION['tasks'], 'id'));
                $idBaru = $lastId + 1;
            } else {
                $idBaru = 1;
            }
            $_SESSION['tasks'][] = ["id" => $idBaru, "title" => $judul, "status" => "belum"];
        }
        header("Location: index.php");
        exit;
    }

    // Logika Update tugas
    if (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $judulBaru = trim($_POST['tugas']);
        foreach ($_SESSION['tasks'] as &$task) {
            if ($task['id'] === $id) {
                $task['title'] = $judulBaru;
                break;
            }
        }
        unset($task);
        header("Location: index.php");
        exit;
    }

    // Logika Toggle status
    if (isset($_GET['toggle'])) {
        $id = (int)$_GET['toggle'];
        foreach ($_SESSION['tasks'] as &$task) {
            if ($task['id'] === $id) {
                $task['status'] = ($task['status'] === "belum") ? "selesai" : "belum";
                break;
            }
        }
        unset($task);
        header("Location: index.php");
        exit;
    }

    // Logika Hapus tugas
    if (isset($_GET['hapus'])) {
        $id = (int)$_GET['hapus'];
        foreach ($_SESSION['tasks'] as $i => $t) {
            if ($t['id'] === $id) {
                unset($_SESSION['tasks'][$i]);
                break;
            }
        }
        $_SESSION['tasks'] = array_values($_SESSION['tasks']);
        header("Location: index.php");
        exit;
    }

    // Logika Ambil data edit (jika ada)
    $editId = null;
    $editValue = "";
    if (isset($_GET['edit'])) {
        $editId = (int)$_GET['edit'];
        foreach ($_SESSION['tasks'] as $t) {
            if ($t['id'] === $editId) {
                $editValue = $t['title'];
                break;
            }
        }
    }

    // Logika Fungsi tampilkan daftar
    function tampilkanDaftar($tasks) {
        
        if (empty($tasks)) {
            echo "<p class='text-gray-500 italic'>Belum ada tugas.</p>";
            return;
        }

        echo "<ul class='space-y-2'>";
        foreach ($tasks as $task) {
            $selesai = $task['status'] === "selesai" ? "line-through text-gray-500" : "";
            $checked = $task['status'] === "selesai" ? "checked" : "";

            $badge = $task['status'] === "selesai" 
                ? "<span class='ml-2 text-xs bg-green-600 text-slate-50 px-2 py-0.5 rounded-full'>Selesai</span>"
                : "<span class='ml-2 text-xs bg-rose-900 text-slate-50 px-2 py-0.5 rounded-full'>Belum</span>";

            echo "
            <li class='flex items-center justify-between bg-gray-100 p-2 rounded'>
                <div class='flex items-center space-x-2'>
                    <input type='checkbox' onchange=\"window.location='?toggle={$task['id']}'\" $checked>
                    <span class='$selesai'>{$task['title']}</span>
                    $badge
                </div>
                <div class='space-x-2'>
                    <a href='?edit={$task['id']}' class='text-blue-500 hover:underline'>Edit</a>
                    <a href='?hapus={$task['id']}' class='text-red-500 hover:underline'>Hapus</a>
                </div>
            </li>";
        }
        echo "</ul>";
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>To Do List Adriansyah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-xl p-6 w-full max-w-md">
        <h1 class="text-2xl text-center font-bold mb-4 text-blue-600">📝 To Do List Adriansyah</h1>

        <!-- Form Tambah / Edit -->
        <form method="POST" class="flex space-x-2 mb-4">
            <input type="text" name="tugas" placeholder="Masukan tugas baru..." 
                    value="<?= htmlspecialchars($editValue) ?>" 
                    class="flex-1 border rounded p-2" required>

            <!-- Logika IF Kalau Ubah Data -->
            <?php if ($editId): ?>
                <input type="hidden" name="id" value="<?= $editId ?>">
                <button type="submit" name="update" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Update</button>
                <a href="index.php" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">Batal</a>
            <?php else: ?>
                <button type="submit" name="tambah" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Tambah</button>
            <?php endif; ?>
        </form>

        <?php tampilkanDaftar($_SESSION['tasks']); ?>
        <footer class="text-center text-gray-400 text-sm mt-6">
            &copy; <?php echo date("Y"); ?> Latihan BNSP
        </footer>
    </div>
</body>
</html>