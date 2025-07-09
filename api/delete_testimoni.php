<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $testimoni_id = intval($input['id'] ?? 0);
    
    if ($testimoni_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID testimoni tidak valid']);
        exit;
    }
    
    // Check if testimoni exists and user has permission
    $stmt = $conn->prepare("SELECT user_id FROM testimoni WHERE id = ?");
    $stmt->execute([$testimoni_id]);
    $testimoni = $stmt->fetch();
    
    if (!$testimoni) {
        echo json_encode(['success' => false, 'message' => 'Testimoni tidak ditemukan']);
        exit;
    }
    
    // Check permission: user can delete their own testimoni, admin can delete any
    if ($_SESSION['role'] !== 'admin' && $testimoni['user_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk menghapus testimoni ini']);
        exit;
    }
    
    // Delete testimoni
    $stmt = $conn->prepare("DELETE FROM testimoni WHERE id = ?");
    
    if ($stmt->execute([$testimoni_id])) {
        echo json_encode([
            'success' => true,
            'message' => 'Testimoni berhasil dihapus'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus testimoni']);
    }
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>