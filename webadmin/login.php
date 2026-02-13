<?php
session_start();

// Prevent back button cache issues
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// If already logged in, redirect to dashboard
if(isset($_SESSION['username'])) {
    header('Location: ../pages/dashboard.php');
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/db_connect.php';
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // VULNERABILITY: SQL Injection - No sanitization!
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    
    try {
        $stmt = $pdo->query($query);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: ../pages/dashboard.php');
            exit();
        } else {
            $error = 'Geçersiz kullanıcı adı veya şifre';
        }
    } catch(PDOException $e) {
        $error = 'Kimlik doğrulama hatası';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aura Sanat Galerisi - Üye Girişi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8 fade-in">
            <h1 class="text-5xl font-light gold-text mb-2">AURA</h1>
            <p class="text-gray-400 text-sm tracking-widest">SANAT GALERİSİ</p>
        </div>
        
        <!-- Login Card -->
        <div class="glass rounded-lg p-8 shadow-2xl fade-in">
            <h2 class="text-2xl font-light text-white mb-2 text-center">Üye Girişi</h2>
            <p class="text-gray-400 text-sm text-center mb-6">Seçkin koleksiyonerler için özel</p>
            
            <?php if($error): ?>
            <div class="alert alert-error text-sm">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-300 text-sm mb-2">Kullanıcı Adı</label>
                    <input type="text" name="username" required 
                           class="w-full bg-black/40 border border-gray-700 rounded px-4 py-3 text-white focus:border-gold-border transition-all">
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-300 text-sm mb-2">Şifre</label>
                    <input type="password" name="password" required 
                           class="w-full bg-black/40 border border-gray-700 rounded px-4 py-3 text-white focus:border-gold-border transition-all">
                </div>
                
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-500 hover:to-yellow-600 text-black font-semibold py-3 rounded transition-all transform hover:scale-105">
                    GALERİYE GİR
                </button>
            </form>
            
        
        <div class="text-center mt-6">
            <a href="../index.php" class="text-gray-500 text-sm hover:text-gold-text transition">← Ana Sayfaya Dön</a>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-gray-600 text-xs">© Copyright Deepreo. Tüm hakları saklıdır.</p>
        </div>
    </div>

    <script src="../assets/js/animations.js"></script>
</body>
</html>
