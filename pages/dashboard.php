<?php
session_start();

// Prevent back button cache issues
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Strong CSP to prevent XSS and injection attacks
header("Content-Security-Policy: " .
    "default-src 'self'; " .
    "script-src 'self' https://cdn.tailwindcss.com; " .
    "style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://fonts.googleapis.com; " .
    "font-src 'self' https://fonts.gstatic.com; " .
    "img-src 'self' data:; " .
    "connect-src 'self'; " .
    "frame-ancestors 'none'; " .
    "base-uri 'self'; " .
    "form-action 'self'; " .
    "upgrade-insecure-requests"
);

// Additional security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

if(!isset($_SESSION['username'])) {
    header('Location: ../webadmin/login.php');
    exit();
}

require_once '../includes/db_connect.php';

// Handle comment submission - Fixed: XSS protection added
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $artwork_id = $_POST['artwork_id'];
    $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
    $username = $_SESSION['username'];
    
    $stmt = $pdo->prepare("INSERT INTO comments (artwork_id, username, comment) VALUES (?, ?, ?)");
    $stmt->execute([$artwork_id, $username, $comment]);
}

// Fetch comments for display
$comments = [];
$stmt = $pdo->query("SELECT * FROM comments ORDER BY created_at DESC");
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$artworks = [
    ['id' => 1, 'title' => 'Midnight Elegance', 'artist' => 'Victoria Laurent', 'price' => '$2,400,000'],
    ['id' => 2, 'title' => 'Golden Horizon', 'artist' => 'Marcus Chen', 'price' => '$1,850,000'],
    ['id' => 3, 'title' => 'Ethereal Dreams', 'artist' => 'Isabella Rossi', 'price' => '$3,200,000'],
    ['id' => 4, 'title' => 'Urban Symphony', 'artist' => 'Alexandre Dubois', 'price' => '$1,650,000'],
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aura Sanat Galerisi - Koleksiyon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="min-h-screen p-6">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-8 fade-in">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-light gold-text">AURA</h1>
                <p class="text-gray-400 text-sm">Özel Koleksiyon</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="profile.php?id=<?php 
                    $stmt_id = $pdo->prepare('SELECT id FROM users WHERE username = ?');
                    $stmt_id->execute([$_SESSION['username']]);
                    echo $stmt_id->fetchColumn();
                ?>" class="text-gray-300 hover:text-yellow-500 transition">
                    Profilim
                </a>
                <span class="text-gray-300">Hoş geldiniz, <span class="gold-text"><?php echo htmlspecialchars($_SESSION['username']); ?></span></span>
                <?php if($_SESSION['role'] == 'admin'): ?>
                <a href="admin_tool.php" class="glass px-4 py-2 rounded text-yellow-500 hover:bg-yellow-900/20 transition">
                    Bilgi
                </a>
                <?php endif; ?>
                <a href="logout.php" class="glass px-4 py-2 rounded text-gray-300 hover:bg-red-900/20 transition">Çıkış</a>
            </div>
        </div>
    </div>

    <!-- Artworks Grid -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <?php foreach($artworks as $art): ?>
        <div class="artwork-card">
            <div class="artwork-img">
                <img src="../assets/img/artwork<?php echo $art['id']; ?>.png" alt="<?php echo $art['title']; ?>" class="w-full h-full object-cover">
            </div>
            <div class="p-6">
                <h3 class="text-2xl font-light text-white mb-1"><?php echo $art['title']; ?></h3>
                <p class="text-gray-400 text-sm mb-2">by <?php echo $art['artist']; ?></p>
                <p class="gold-text text-xl mb-4"><?php echo $art['price']; ?></p>
                
                <!-- Comment Form -->
                <details class="mt-4">
                    <summary class="cursor-pointer text-yellow-600 hover:text-yellow-500 text-sm">Koleksiyoner Notları</summary>
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="artwork_id" value="<?php echo $art['id']; ?>">
                        <textarea name="comment" rows="2" placeholder="Düşüncelerinizi paylaşın..." 
                                  class="w-full bg-black/40 border border-gray-700 rounded px-3 py-2 text-white text-sm mb-2"></textarea>
                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-black px-4 py-1 rounded text-sm">
                            Not Ekle
                        </button>
                    </form>
                    
                    <!-- Display Comments - Fixed: XSS protection added -->
                    <div class="mt-3 space-y-2">
                        <?php foreach($comments as $c): ?>
                            <?php if($c['artwork_id'] == $art['id']): ?>
                            <div class="bg-black/30 rounded p-2 text-sm">
                                <span class="gold-text"><?php echo htmlspecialchars($c['username'], ENT_QUOTES, 'UTF-8'); ?>:</span>
                                <span class="text-gray-300"><?php echo htmlspecialchars($c['comment'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </details>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center text-gray-600 text-xs">
        <p>© Copyright Deepreo. Tüm hakları saklıdır.</p>
    </div>

    <script src="../assets/js/animations.js"></script>
</body>
</html>
