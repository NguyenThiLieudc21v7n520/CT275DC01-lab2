<?php
/* Đoạn mã xử lý PHP. */

define('TITLE', 'Thêm Trích dẫn');

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/footer.php';

$has_access = ensure_admin_access();
$error_message = null;
$success_message = null;
$reason = null;

// Khởi tạo biến $form_data để lưu dữ liệu nhập từ form
$form_data = [
    'quote' => '',
    'source' => '',
    'favorite' => 0
];

if (!$has_access) {
    $error_message = 'Bạn không có quyền truy cập trang này';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Đọc thông tin từ $_POST vào $form_data
    $form_data['quote'] = trim($_POST['quote'] ?? '');
    $form_data['source'] = trim($_POST['source'] ?? '');
    $form_data['favorite'] = !empty($_POST['favorite']) ? 1 : 0;

    if (!empty($form_data['quote']) && !empty($form_data['source'])) {
        try {
            $pdo = get_database_connection();
            if ($pdo instanceof PDO) {
                $query = 'INSERT INTO quotes (quote, source, favorite, date_entered) VALUES (:quote, :source, :favorite, NOW())';
                $statement = $pdo->prepare($query);
                $statement->execute([
                    ':quote' => $form_data['quote'],
                    ':source' => $form_data['source'],
                    ':favorite' => $form_data['favorite']
                ]);
                
                $success_message = 'Thêm trích dẫn thành công!';
                
                // Reset lại $form_data để làm trống form sau khi thêm thành công
                $form_data = [
                    'quote' => '',
                    'source' => '',
                    'favorite' => 0
                ];
            }
        } catch (PDOException $e) {
            $error_message = 'Không thể thêm trích dẫn';
            $reason = $e->getMessage();
        }
    } else {
        $error_message = 'Vui lòng nhập đầy đủ Trích dẫn và Nguồn!';
    }
}
?>

<!-- Đoạn mã HTML trình bày nội dung trang web. -->
<?php render_page_header(); ?>

<h2>Thêm Trích dẫn mới</h2>

<?php if (!empty($error_message)): ?>
    <?php include __DIR__ . '/../partials/show_error.php'; ?>
<?php endif; ?>

<?php if (!empty($success_message)): ?>
    <p style="color: green; font-weight: bold;"><?= html_escape($success_message) ?></p>
<?php endif; ?>

<?php if ($has_access): ?>
    <form action="add_quote.php" method="post">
        <p>
            <label>Trích dẫn:<br>
            <textarea name="quote" rows="5" cols="40"><?= html_escape($form_data['quote']) ?></textarea>
            </label>
        </p>
        <p>
            <label>Nguồn:<br>
            <input type="text" name="source" size="40" value="<?= html_escape($form_data['source']) ?>" />
            </label>
        </p>
        <p>
            <label>
            <input type="checkbox" name="favorite" value="1" <?= $form_data['favorite'] ? 'checked' : '' ?> /> Đánh dấu là Yêu thích?
            </label>
        </p>
        <p><input type="submit" name="submit" value="Thêm trích dẫn" /></p>
    </form>
<?php endif; ?>

<?php render_page_footer(); ?>