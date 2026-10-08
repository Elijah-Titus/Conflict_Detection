<?php
// Path back to the project root (set by the page, or by header.php)
$base = $base ?? '../';

// List of JavaScript files the page wants loaded at the bottom
$pageScripts = $pageScripts ?? [];
?>

<?php foreach ($pageScripts as $script): ?>
    <script src="<?= $base ?><?= htmlspecialchars($script) ?>"></script>
<?php endforeach; ?>

</body>
</html>