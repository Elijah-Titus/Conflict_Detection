<?php $pageScripts = $pageScripts ?? []; ?>

    </main>   <!-- closes .user-main -->
</div>        <!-- closes .user-layout -->

<?php foreach ($pageScripts as $script): ?>
    <script src="<?= $base . htmlspecialchars($script) ?>"></script>
<?php endforeach; ?>

</body>
</html>