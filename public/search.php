<?php

define('TITLE', 'Tìm kiếm Trích dẫn');

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/footer.php';

$error_message = null;
$reason = null;
$sources = [];
$results = [];

$keyword = trim($_GET['keyword'] ?? '');
$selected_source = trim($_GET['source'] ?? '');

try {
    $pdo = get_database_connection();

    if ($pdo instanceof PDO) {
        $source_stmt = $pdo->query('SELECT DISTINCT source FROM quotes ORDER BY source ASC');
        $sources = $source_stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($keyword) || !empty($selected_source)) {
            $query = 'SELECT id, quote, source, favorite FROM quotes WHERE 1=1';
            $params = [];

            if (!empty($keyword)) {
                $query .= ' AND quote ILIKE :keyword';
                $params[':keyword'] = '%' . $keyword . '%';
            }

            if (!empty($selected_source)) {
                $query .= ' AND source = :source';
                $params[':source'] = $selected_source;
            }

            $query .= ' ORDER BY id DESC';

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
        }
    }
} catch (PDOException $e) {
    $error_message = 'Không thể truy vấn CSDL';
    $reason = $e->getMessage();
}
?>

<?php render_page_header(); ?>

<h2>Tìm kiếm Trích dẫn</h2>

<?php if (!empty($error_message)): ?>
    <?php include __DIR__ . '/../partials/show_error.php'; ?>
<?php endif; ?>

<form action="search.php" method="get">
    <p>
        <label>Từ khóa trích dẫn:<br>
            <input type="text" name="keyword" size="40" value="<?= html_escape($keyword) ?>" />
        </label>
    </p>

    <p>
        <label>Nguồn / Tác giả:<br>
            <select name="source">
                <option value="">-- Tất cả các nguồn --</option>
                <?php foreach ($sources as $src): ?>
                    <option value="<?= html_escape($src) ?>" <?= $selected_source === $src ? 'selected' : '' ?>>
                        <?= html_escape($src) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </p>

    <p><input type="submit" value="Tìm kiếm" /></p>
</form>

<?php if (!empty($keyword) || !empty($selected_source)): ?>
    <hr>
    <h3>Kết quả tìm kiếm</h3>

    <?php if (!empty($results)): ?>
        <?php foreach ($results as $item): ?>
            <div>
                <blockquote><?= html_escape($item['quote']) ?></blockquote>
                <p>- <em><?= html_escape($item['source']) ?></em>
                    <?php if (!empty($item['favorite'])): ?>
                        <strong> | Yêu thích!</strong>
                    <?php endif; ?>
                </p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Không tìm thấy trích dẫn nào phù hợp.</p>
    <?php endif; ?>
<?php endif; ?>

<?php render_page_footer(); ?>