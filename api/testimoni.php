<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $judul = trim($_POST['judul'] ?? '');
    $testimoni = trim($_POST['testimoni'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);
    $kategori = trim($_POST['kategori'] ?? '');
    $user_id = $_SESSION['user_id'];
    
    // Validation
    if (empty($judul) || empty($testimoni) || empty($kategori)) {
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
        exit;
    }
    
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Rating harus antara 1-5']);
        exit;
    }
    
    if (strlen($judul) < 5) {
        echo json_encode(['success' => false, 'message' => 'Judul minimal 5 karakter']);
        exit;
    }
    
    if (strlen($testimoni) < 10) {
        echo json_encode(['success' => false, 'message' => 'Testimoni minimal 10 karakter']);
        exit;
    }
    
    // Insert testimoni
    $stmt = $conn->prepare("INSERT INTO testimoni (user_id, judul, testimoni, rating, kategori) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$user_id, $judul, $testimoni, $rating, $kategori])) {
        echo json_encode([
            'success' => true,
            'message' => 'Testimoni berhasil ditambahkan'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan testimoni']);
    }
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>