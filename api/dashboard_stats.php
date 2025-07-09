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
    
    $stats = [];
    
    if ($_SESSION['role'] === 'admin') {
        // Admin statistics
        
        // Total testimoni
        $stmt = $conn->prepare("SELECT COUNT(*) FROM testimoni WHERE status = 'active'");
        $stmt->execute();
        $stats['total_testimoni'] = $stmt->fetchColumn();
        
        // Total users
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'user'");
        $stmt->execute();
        $stats['total_users'] = $stmt->fetchColumn();
        
        // Average rating
        $stmt = $conn->prepare("SELECT AVG(rating) FROM testimoni WHERE status = 'active'");
        $stmt->execute();
        $stats['avg_rating'] = round($stmt->fetchColumn(), 1);
        
        // Recent testimoni (last 30 days)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM testimoni WHERE status = 'active' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stmt->execute();
        $stats['recent_testimoni'] = $stmt->fetchColumn();
        
        // Testimoni by category
        $stmt = $conn->prepare("SELECT kategori, COUNT(*) as count FROM testimoni WHERE status = 'active' GROUP BY kategori ORDER BY count DESC");
        $stmt->execute();
        $stats['by_category'] = $stmt->fetchAll();
        
        // Recent activity
        $stmt = $conn->prepare("
            SELECT 
                t.id,
                t.judul,
                t.rating,
                t.kategori,
                t.created_at,
                u.username
            FROM testimoni t
            JOIN users u ON t.user_id = u.id
            WHERE t.status = 'active'
            ORDER BY t.created_at DESC
            LIMIT 5
        ");
        $stmt->execute();
        $stats['recent_activity'] = $stmt->fetchAll();
        
    } else {
        // User statistics
        $user_id = $_SESSION['user_id'];
        
        // User's testimoni count
        $stmt = $conn->prepare("SELECT COUNT(*) FROM testimoni WHERE user_id = ? AND status = 'active'");
        $stmt->execute([$user_id]);
        $stats['my_testimoni'] = $stmt->fetchColumn();
        
        // User's average rating
        $stmt = $conn->prepare("SELECT AVG(rating) FROM testimoni WHERE user_id = ? AND status = 'active'");
        $stmt->execute([$user_id]);
        $stats['my_avg_rating'] = round($stmt->fetchColumn(), 1);
        
        // User's testimoni by category
        $stmt = $conn->prepare("SELECT kategori, COUNT(*) as count FROM testimoni WHERE user_id = ? AND status = 'active' GROUP BY kategori");
        $stmt->execute([$user_id]);
        $stats['my_by_category'] = $stmt->fetchAll();
        
        // User's recent testimoni
        $stmt = $conn->prepare("
            SELECT 
                id,
                judul,
                rating,
                kategori,
                created_at
            FROM testimoni
            WHERE user_id = ? AND status = 'active'
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$user_id]);
        $stats['my_recent'] = $stmt->fetchAll();
    }
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>