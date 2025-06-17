<?php
$pageTitle = "教材详情";
require_once 'header.php'; // 确保 header.php 中有 session_start()

// 显示会话消息
if (isset($_SESSION['success_message'])) {
    echo '<div class="container mt-3"><div class="alert alert-success">' 
         . $_SESSION['success_message'] 
         . '</div></div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="container mt-3"><div class="alert alert-danger">' 
         . $_SESSION['error_message'] 
         . '</div></div>';
    unset($_SESSION['error_message']);
}

// 获取教材信息
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$book = getBookById($bookId);

if (!$book) {
    echo "<div class='alert alert-danger'>教材不存在</div>";
    require_once 'footer.php';
    exit;
}
?>


<div class="row">
    <div class="col-md-6">
        <div id="bookImages" class="carousel slide">
            <div class="carousel-inner">
                <?php foreach ($book['images'] as $index => $image): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= htmlspecialchars($image['image_path']) ?>" class="d-block w-100" alt="教材图片">
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#bookImages" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#bookImages" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
    
    <div class="col-md-6">
        <h2><?= htmlspecialchars($book['title']) ?></h2>
        <p>作者: <?= htmlspecialchars($book['author']) ?></p>
        <p>ISBN: <?= htmlspecialchars($book['isbn']) ?></p>
        <p>价格: <span class="text-danger fw-bold">¥<?= number_format($book['price'], 2) ?></span></p>
        <p>状态: <span class="badge bg-<?= $book['status'] === 'available' ? 'success' : 'danger' ?>">
            <?= $book['status'] === 'available' ? '可购买' : '已售出' ?>
        </span></p>
        
        <div class="mt-4">
            <button id="addToCart" class="btn btn-primary btn-lg me-2" data-book-id="<?= $bookId ?>">
                <i class="fas fa-cart-plus me-1"></i>加入购物车
            </button>
            <button id="buyNow" class="btn btn-success btn-lg" data-book-id="<?= $bookId ?>">
                <i class="fas fa-bolt me-1"></i>立即购买
            </button>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">教材描述</div>
            <div class="card-body">
                <?= nl2br(htmlspecialchars($book['description'])) ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 加入购物车
    $('#addToCart').click(function() {
        const bookId = $(this).data('book-id');
        $.post('api/cart.php', { action: 'add', book_id: bookId }, function(response) {
            alert(response.message);
        }, 'json');
    });
    
    // 立即购买
    $('#buyNow').click(function() {
        const bookId = $(this).data('book-id');
        window.location.href = 'checkout.php?book_id=' + bookId;
    });
});
</script>

<?php require_once 'footer.php'; ?>