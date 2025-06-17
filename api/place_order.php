<?php
require_once '../config.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => '请先登录']));
}

// 获取数据
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$bookId = (int)($input['book_id'] ?? 0);
$address = trim($input['address'] ?? '');

try {
    // 验证教材
    $book = $db->query("SELECT * FROM books WHERE book_id = $bookId AND status = 'available'")->fetch_assoc();
    if (!$book) {
        throw new Exception("教材不可用");
    }

    // 准备订单
    $db->begin_transaction();
    
    $sql = "INSERT INTO orders (...) VALUES (...)";
    $stmt = $db->prepare($sql);
    
    // 绑定参数
    $totalPrice = $book['price'];
    $stmt->bind_param("iiiids", 
        $_SESSION['user_id'],
        $book['user_id'],
        $bookId,
        1, // 数量
        $totalPrice,
        $address
    );
    
    if (!$stmt->execute()) {
        throw new Exception("订单创建失败: " . $stmt->error);
    }
    
    // 更新教材状态
    $db->query("UPDATE books SET status = 'sold' WHERE book_id = $bookId");
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'order_id' => $db->insert_id
    ]);
    
} catch (Exception $e) {
    $db->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}