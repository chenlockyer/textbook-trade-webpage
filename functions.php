<?php
require_once 'config.php';

// 用户相关函数
function registerUser($username, $password, $email, $studentId) {
    global $db;
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("INSERT INTO users (username, password, email, student_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $hashedPassword, $email, $studentId);
    
    return $stmt->execute();
}

function loginUser($username, $password) {
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    
    return false;
}

// 教材相关函数
function addBook($userId, $data) {
    global $db;
    
    // 调试：记录传入数据
    error_log("尝试添加教材数据：" . print_r($data, true));
    
    $sql = "INSERT INTO books (
        user_id, title, author, isbn, edition, publisher, 
        price, `book_condition`, description, category_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        error_log("SQL准备失败: " . $db->error);
        throw new Exception("数据库操作失败");
    }
    
    // 绑定参数
    $stmt->bind_param(
        "isssssdssi", 
        $userId,
        $data['title'],
        $data['author'],
        $data['isbn'],
        $data['edition'],
        $data['publisher'],
        $data['price'],
        $data['book_condition'],
        $data['description'],
        $data['category_id']
    );
    
    if (!$stmt->execute()) {
        error_log("执行失败: " . $stmt->error);
        throw new Exception("教材保存失败: " . $stmt->error);
    }
    
    $bookId = $db->insert_id;
    error_log("教材添加成功，ID: " . $bookId);
    return $bookId;
}

function getPopularBooks($limit = 10) {
    global $db;
    
    $stmt = $db->prepare("SELECT b.*, u.username 
                         FROM books b 
                         JOIN users u ON b.user_id = u.user_id 
                         WHERE b.status = 'available' 
                         ORDER BY b.created_at DESC 
                         LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    
    $books = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    
    return $books;
}

function getBookImages($bookId) {
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM book_images WHERE book_id = ? ORDER BY is_cover DESC");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    
    $images = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    
    return $images;
}

function getBookCoverImage($bookId) {
    global $db;
    $stmt = $db->prepare("SELECT image_path FROM book_images WHERE book_id = ? AND is_cover = 1 LIMIT 1");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        return $result->fetch_assoc()['image_path'];
    }
    return 'assets/images/default-book.jpg'; // 默认图片路径
}

function getAllCategories() {
    global $db;
    
    $result = $db->query("SELECT * FROM categories ORDER BY name");
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    return $categories;
}

// 
function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// 图片上传处理
function uploadBookImages($bookId, $files) {
    global $db;
    
    $uploadDir = 'uploads/books/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $maxFiles = 5;
    $uploadedCount = 0;
    
    for ($i = 0; $i < count($files['name']) && $uploadedCount < $maxFiles; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid('book_') . '.' . $ext;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($files['tmp_name'][$i], $destination)) {
                $isCover = ($uploadedCount === 0) ? 1 : 0;
                
                $stmt = $db->prepare("INSERT INTO book_images (book_id, image_path, is_cover) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $bookId, $destination, $isCover);
                $stmt->execute();
                
                $uploadedCount++;
            }
        }
    }
    
    return $uploadedCount;
}

function getBookById($bookId) {
    global $db;

    // 查询书籍信息
    $sql = "SELECT * FROM books WHERE book_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        throw new Exception("数据库查询失败: " . $db->error);
    }

    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return null; // 书籍不存在
    }

    $book = $result->fetch_assoc();

    // 查询书籍图片（假设图片存储在 book_images 表中）
    $sqlImages = "SELECT image_path FROM book_images WHERE book_id = ?";
    $stmtImages = $db->prepare($sqlImages);
    if (!$stmtImages) {
        throw new Exception("图片查询失败: " . $db->error);
    }

    $stmtImages->bind_param("i", $bookId);
    $stmtImages->execute();
    $resultImages = $stmtImages->get_result();

    $book['images'] = [];
    while ($row = $resultImages->fetch_assoc()) {
        $book['images'][] = $row;
    }

    return $book;
}
