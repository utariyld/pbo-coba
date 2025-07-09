<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Pagination
    $page = intval($_GET['page'] ?? 1);
    $limit = 6;
    $offset = ($page - 1) * $limit;
    
    // Get total count
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM testimoni t WHERE t.status = 'active'");
    $countStmt->execute();
    $total = $countStmt->fetchColumn();
    
    // Get testimoni with user info
    $stmt = $conn->prepare("
        SELECT 
            t.id,
            t.judul,
            t.testimoni,
            t.rating,
            t.kategori,
            t.created_at,
            u.username
        FROM testimoni t
        JOIN users u ON t.user_id = u.id
        WHERE t.status = 'active'
        ORDER BY t.created_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $stmt->execute([$limit, $offset]);
    $testimoniList = $stmt->fetchAll();
    
    $hasMore = ($offset + $limit) < $total;
    
    echo json_encode([
        'success' => true,
        'data' => $testimoniList,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'total_items' => $total,
            'items_per_page' => $limit,
            'has_more' => $hasMore
        ]
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>