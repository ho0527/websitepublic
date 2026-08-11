<?php
/**
 * A1 - 主辦者登入頁
 *   - GET  ：顯示登入表單
 *   - POST ：驗證帳密，成功後導向 Manage events
 */

declare(strict_types=1);

require_once __DIR__ . '/inc/auth.php';

start_session();

// 已登入就直接進後台
if (current_organizer() !== null) {
    redirect('events/index.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    // 只查 organizers 資料表，因此使用參加者帳號登入必定失敗
    $organizer = db_one('SELECT * FROM `organizers` WHERE `email` = ?', [$email]);

    if ($organizer !== null && password_verify($password, (string) $organizer['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['organizer_id'] = (int) $organizer['id'];
        redirect('events/index.php');
    }

    $error = 'Email or password not correct';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Event Backend</title>

    <base href="<?= e(base_path()) ?>">
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="assets/css/custom.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-6 mx-sm-auto px-4">
            <div class="pt-3 pb-2 mb-3 border-bottom text-center">
                <h1 class="h2">WorldSkills Event Platform</h1>
            </div>

            <form class="form-signin" method="post" action="index.php">
                <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
                <?php endif; ?>

                <label for="inputEmail" class="sr-only">Email</label>
                <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email"
                       value="<?= e($email) ?>" autofocus>

                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password">
                <button class="btn btn-lg btn-primary btn-block" id="login" type="submit">Sign in</button>
            </form>

        </main>
    </div>
</div>
</body>
</html>
