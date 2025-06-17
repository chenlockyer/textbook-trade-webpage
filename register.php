<?php
$pageTitle = "注册";
require_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
    $studentId = $_POST['student_id'] ?? '';
    
    if (registerUser($username, $password, $email, $studentId)) {
        $_SESSION['success_message'] = "注册成功，请登录";
        header("Location: login.php");
        exit;
    } else {
        $error = "注册失败，用户名或邮箱可能已被使用";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">用户注册</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">用户名</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">密码</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">电子邮箱</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="student_id" class="form-label">学号</label>
                        <input type="text" class="form-control" id="student_id" name="student_id" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">注册</button>
                </form>
                
                <div class="mt-3 text-center">
                    <a href="login.php">已有账号？立即登录</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>