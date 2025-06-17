<?php
require_once 'config.php';

// 测试数据库连接
$testQuery = $db->query("SELECT 1");
if (!$testQuery) {
    die("数据库连接失败: " . $db->error);
}else{
    $error = "DB连接成功";
}

$pageTitle = "发布教材";
require_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$categories = getAllCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'author' => trim($_POST['author'] ?? ''),
        'isbn' => trim($_POST['isbn'] ?? ''),
        'edition' => trim($_POST['edition'] ?? ''),
        'publisher' => trim($_POST['publisher'] ?? ''),
        'price' => (float) ($_POST['price'] ?? 0),
        'book_condition' => trim($_POST['condition'] ?? 'good'),
        'description' => trim($_POST['description'] ?? ''),
        'category_id' => (int) ($_POST['category_id'] ?? 0)
    ];
    
    try {
        $bookId = addBook($_SESSION['user_id'], $data);
        
        // 处理图片上传（确保 uploadBookImages 也有异常处理）
        if (!empty($_FILES['images']['name'][0])) {
            uploadBookImages($bookId, $_FILES['images']);
        }
        
        $_SESSION['success_message'] = "教材发布成功";
        header("Location: book_detail.php?id=$bookId");
        exit;
    } catch (Exception $e) {
        $error = "发布失败: " . $e->getMessage();  // 显示具体错误
    }
}

?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">发布二手教材</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">教材名称*</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="author" class="form-label">作者</label>
                            <input type="text" class="form-control" id="author" name="author">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="isbn" class="form-label">ISBN</label>
                            <input type="text" class="form-control" id="isbn" name="isbn">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edition" class="form-label">版次</label>
                            <input type="text" class="form-control" id="edition" name="edition">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="publisher" class="form-label">出版社</label>
                            <input type="text" class="form-control" id="publisher" name="publisher">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">价格*</label>
                            <div class="input-group">
                                <span class="input-group-text">¥</span>
                                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="condition" class="form-label">新旧程度*</label>
                            <select class="form-select" id="condition" name="condition" required>
                                <option value="new">全新</option>
                                <option value="like_new">几乎全新</option>
                                <option value="good" selected>良好</option>
                                <option value="acceptable">可用</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">分类*</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">请选择分类</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">详细描述*</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="images" class="form-label">教材图片</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                        <div class="form-text">最多上传5张图片，第一张将作为封面</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">发布教材</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>