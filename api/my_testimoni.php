<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $user_id = $_SESSION['user_id'];
    
    // Get user's testimoni
    $stmt = $conn->prepare("
        SELECT 
            id,
            judul,
            testimoni,
            rating,
            kategori,
            status,
            created_at,
            updated_at
        FROM testimoni
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    
    $stmt->execute([$user_id]);
    $testimoniList = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $testimoniList
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>