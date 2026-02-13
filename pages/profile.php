<?php
session_start();

// Prevent back button cache issues
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['username'])) {
    header('Location: ../webadmin/login.php');
    exit();
}

require_once '../includes/db_connect.php';

// Create profile_notes table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS profile_notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        profile_user_id INT NOT NULL,
        note_author VARCHAR(50) NOT NULL,
        note TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch(PDOException $e) {
    // Table might already exist, continue
}

// Handle profile note submission - VULNERABILITY: Stored XSS
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profile_note'])) {
    $profile_user_id = $_POST['profile_user_id'];
    $note = $_POST['profile_note']; // No sanitization!
    $note_author = $_SESSION['username'];
    
    $stmt = $pdo->prepare("INSERT INTO profile_notes (profile_user_id, note_author, note) VALUES (?, ?, ?)");
    $stmt->execute([$profile_user_id, $note_author, $note]);
    
    // Redirect to prevent form resubmission
    header("Location: profile.php?id=" . $profile_user_id);
    exit();
}

// VULNERABILITY: IDOR - No authorization check!
// User can view any profile by changing the ID parameter
$user_id = isset($_GET['id']) ? $_GET['id'] : 1;

try {
    $stmt = $pdo->prepare("SELECT id, username, role, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$profile) {
        $error = "Kullanıcı bulunamadı";
    }
} catch(PDOException $e) {
    $error = "Profil yüklenirken hata oluştu";
}

// Get user's comments
$comments = [];
if($profile) {
    $stmt = $pdo->prepare("SELECT c.*, c.created_at FROM comments c WHERE c.username = ? ORDER BY c.created_at DESC LIMIT 10");
    $stmt->execute([$profile['username']]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get profile notes
$profile_notes = [];
if($profile) {
    $stmt = $pdo->prepare("SELECT * FROM profile_notes WHERE profile_user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$profile['id']]);
    $profile_notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get current user's ID for comparison
$current_user_stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$current_user_stmt->execute([$_SESSION['username']]);
$current_user = $current_user_stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aura Sanat Galerisi - Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 fade-in">
            <div>
                <h1 class="text-4xl font-light gold-text">PROFİL</h1>
                <p class="text-gray-400 text-sm">Kullanıcı Bilgileri</p>
            </div>
            <a href="dashboard.php" class="glass px-4 py-2 rounded text-gray-300 hover:bg-gray-800 transition">
                ← Galeriye Dön
            </a>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-error mb-6">
                <?php echo $error; ?>
            </div>
        <?php else: ?>
            <!-- Profile Card -->
            <div class="glass rounded-lg p-8 mb-6 fade-in">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-3xl font-light text-white mb-2"><?php echo htmlspecialchars($profile['username']); ?></h2>
                        <div class="flex items-center gap-4 text-gray-400">
                            <span class="<?php echo $profile['role'] == 'admin' ? 'text-yellow-500' : 'text-gray-400'; ?>">
                                <?php echo $profile['role'] == 'admin' ? 'Yönetici' : 'Koleksiyoner'; ?>
                            </span>
                            <span>•</span>
                            <span>Üye ID: #<?php echo $profile['id']; ?></span>
                        </div>
                    </div>
                    
                    <?php if($current_user['id'] == $profile['id']): ?>
                        <span class="glass px-3 py-1 rounded text-yellow-500 text-sm">Sizin Profiliniz</span>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-black/30 rounded p-4">
                        <p class="text-gray-500 text-sm mb-1">Kullanıcı Adı</p>
                        <p class="text-white text-lg"><?php echo htmlspecialchars($profile['username']); ?></p>
                    </div>
                    
                    <div class="bg-black/30 rounded p-4">
                        <p class="text-gray-500 text-sm mb-1">Rol</p>
                        <p class="text-white text-lg"><?php echo $profile['role'] == 'admin' ? 'Yönetici' : 'Üye'; ?></p>
                    </div>
                    
                    <div class="bg-black/30 rounded p-4">
                        <p class="text-gray-500 text-sm mb-1">Üyelik Tarihi</p>
                        <p class="text-white text-lg"><?php echo date('d.m.Y', strtotime($profile['created_at'])); ?></p>
                    </div>
                    
                    <div class="bg-black/30 rounded p-4">
                        <p class="text-gray-500 text-sm mb-1">Toplam Yorum</p>
                        <p class="text-white text-lg"><?php echo count($comments); ?></p>
                    </div>
                </div>

                <!-- Recent Comments -->
                <?php if(count($comments) > 0): ?>
                <div class="border-t border-gray-700 pt-6">
                    <h3 class="text-xl font-light text-white mb-4">Son Yorumlar</h3>
                    <div class="space-y-3">
                        <?php foreach($comments as $comment): ?>
                        <div class="bg-black/30 rounded p-3">
                            <p class="text-gray-300 text-sm mb-1"><?php echo $comment['comment']; ?></p>
                            <p class="text-gray-500 text-xs"><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Profile Notes Section -->
            <div class="glass rounded-lg p-8 mb-6 fade-in">
                <h3 class="text-2xl font-light text-white mb-4">Profil Notları</h3>
                
                <!-- Add Note Form -->
                <form method="POST" class="mb-6">
                    <input type="hidden" name="profile_user_id" value="<?php echo $profile['id']; ?>">
                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm mb-2">Bu profil hakkında not ekle</label>
                        <textarea name="profile_note" rows="3" placeholder="Notunuzu yazın..." required
                                  class="w-full bg-black/40 border border-gray-700 rounded px-4 py-3 text-white focus:border-yellow-600 focus:outline-none"></textarea>
                    </div>
                    <button type="submit" 
                            class="bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-500 hover:to-yellow-600 text-black font-semibold px-6 py-2 rounded transition-all">
                        Not Ekle
                    </button>
                </form>

                <!-- Display Profile Notes - VULNERABILITY: XSS -->
                <?php if(count($profile_notes) > 0): ?>
                <div class="border-t border-gray-700 pt-6">
                    <h4 class="text-lg font-light text-white mb-4">Tüm Notlar (<?php echo count($profile_notes); ?>)</h4>
                    <div class="space-y-3">
                        <?php foreach($profile_notes as $note): ?>
                        <div class="bg-black/30 rounded p-4">
                            <div class="flex items-start justify-between mb-2">
                                <span class="gold-text font-semibold"><?php echo $note['note_author']; ?></span>
                                <span class="text-gray-500 text-xs"><?php echo date('d.m.Y H:i', strtotime($note['created_at'])); ?></span>
                            </div>
                            <p class="text-gray-300"><?php echo $note['note']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-6">
                    <p class="text-gray-500">Henüz not eklenmemiş.</p>
                </div>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>

    <script src="../assets/js/animations.js"></script>
</body>
</html>
