<?php
session_start();

// Prevent back button cache issues
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../webadmin/login.php');
    exit();
}

$output = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['table_name'])) {
    $table_name = $_POST['table_name'];
    
    // VULNERABILITY: OS Command Injection - No sanitization!
    // Using 'type' command to read file contents
    $command = 'type "..\assets\info\\' . $table_name . '.txt"';
    $output = shell_exec($command . ' 2>&1');
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aura Sanat Galerisi - Bilgi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 fade-in">
            <div>
                <h1 class="text-4xl font-light gold-text">BİLGİ</h1>
                <p class="text-gray-400 text-sm">Sanat Eseri Bilgi Sistemi</p>
            </div>
            <a href="dashboard.php" class="glass px-4 py-2 rounded text-gray-300 hover:bg-gray-800 transition">
                ← Galeriye Dön
            </a>
        </div>

        <!-- Tool Card -->
        <div class="glass rounded-lg p-8 fade-in">
            <div class="mb-6">
                <p class="text-gray-300 mb-4">
                    Tablo adını girerek bilgi sorgulayın. 
                    Bu sistem koleksiyonlarımız hakkında detaylı bilgi sağlar.
                </p>
                <p class="text-gray-400 text-sm">
                    Mevcut eserler: Midnight Elegance, Golden Horizon, Ethereal Dreams, Urban Symphony
                </p>
            </div>

            <form method="POST" class="mb-6">
                <label class="block text-gray-300 text-sm mb-2">Tablo Adı:</label>
                <div class="flex gap-2">
                    <input type="text" name="table_name" placeholder="örn: Midnight Elegance, Golden Horizon ..." 
                           class="flex-1 bg-black/40 border border-gray-700 rounded px-4 py-3 text-white focus:border-yellow-600 focus:outline-none">
                    <button type="submit" 
                            class="bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-500 hover:to-yellow-600 text-black font-semibold px-6 py-3 rounded transition-all">
                        BİLGİ AL
                    </button>
                </div>
            </form>

            <?php if($output !== null && $output !== ''): ?>
            <div class="bg-black/60 border border-gray-700 rounded p-4">
                <h3 class="text-yellow-600 text-sm mb-2">BİLGİ ÇIKTISI:</h3>
                <pre class="text-gray-300 text-xs overflow-x-auto"><?php echo htmlspecialchars($output); ?></pre>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <script src="../assets/js/animations.js"></script>
</body>
</html>
