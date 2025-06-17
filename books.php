<?php
require_once 'header.php';

// 获取所有教材
function getBooks() {
    global $db;
    $query = "SELECT b.*, u.username 
              FROM books b 
              JOIN users u ON b.user_id = u.user_id
              WHERE b.status = 'available'
              ORDER BY b.created_at DESC";
    $result = $db->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$books = getBooks();
?>

<div class="container mt-4">
    <h2>教材列表</h2>
    <div class="row">
        <?php foreach($books as $book): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="<?= getBookCoverImage($book['book_id']) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($book['author']) ?></p>
                    <p class="price">¥<?= number_format($book['price'], 2) ?></p>
                </div>
                <div class="card-footer">
                    <a href="book_detail.php?id=<?= $book['book_id'] ?>" class="btn btn-primary">查看详情</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>