<?php if (!empty($error_message)): ?>
    <div style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
        <strong>Lỗi:</strong> <?= html_escape($error_message) ?>
        <?php if (!empty($reason)): ?>
            <br><small><em>Chi tiết: <?= html_escape($reason) ?></em></small>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!empty($success_message)): ?>
    <div style="color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
        <strong>Thành công:</strong> <?= html_escape($success_message) ?>
    </div>
<?php endif; ?>