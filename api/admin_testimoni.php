<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses tidak diizinkan']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Get all testimoni with user info
    $stmt = $conn->prepare("
        SELECT 
            t.id,
            t.judul,
            t.testimoni,
            t.rating,
            t.kategori,
            t.status,
            t.created_at,
            t.updated_at,
            u.username,
            u.email
        FROM testimoni t
        JOIN users u ON t.user_id = u.id
        ORDER BY t.created_at DESC
    ");
    
    $stmt->execute();
    $testimoniList = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $testimoniList
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>