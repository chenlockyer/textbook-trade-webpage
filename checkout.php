<?php
require_once 'header.php';

$bookId = (int)$_GET['book_id'];
$book = getBookById($bookId);

if (!$book || $book['status'] !== 'available') {
    die('<div class="alert alert-danger">教材不可购买</div>');
}
?>

<div class="container mt-4">
    <h2>确认订单</h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5><?= htmlspecialchars($book['title']) ?></h5>
                    <p>价格: ¥<?= number_format($book['price'], 2) ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <form id="checkoutForm">
                <input type="hidden" name="book_id" value="<?= $bookId ?>">
                
                <div class="mb-3">
                    <label class="form-label">收货地址</label>
                    <input type="text" name="address" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">确认支付</button>
            </form>
        </div>
    </div>
</div>

<script>
$('#checkoutForm').submit(function(e) {
    e.preventDefault();
    
    $.post('api/place_order.php', $(this).serialize(), function(response) {
        if (response.success) {
            window.location.href = 'order_success.php?id=' + response.order_id;
        } else {
            alert(response.message);
        }
    }, 'json');
});
</script>

<?php require_once 'footer.php'; ?>