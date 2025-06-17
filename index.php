<?php
$pageTitle = "首页";
require_once 'header.php';

// 获取热门教材
$popularBooks = getPopularBooks(8);
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="p-5 bg-light rounded-3">
            <h1 class="display-4">二手教材交易平台</h1>
            <p class="lead">买卖教材更轻松，知识传递更环保</p>
            <a href="books.php" class="btn btn-primary btn-lg">浏览教材</a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-success btn-lg ms-2">立即注册</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">热门教材</h2>
    </div>
    
    <?php foreach ($popularBooks as $book): ?>
    <div class="col-md-3 mb-4">
        <div class="card h-100">
            <img src="<?= getBookCoverImage($book['book_id']) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                <p class="card-text text-muted"><?= htmlspecialchars($book['author']) ?></p>
                <p class="text-danger fw-bold">¥<?= number_format($book['price'], 2) ?></p>
            </div>
            <div class="card-footer bg-white">
                <a href="book_detail.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-outline-primary">查看详情</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'footer.php'; ?>