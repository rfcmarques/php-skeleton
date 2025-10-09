<?= partial('head') ?>

<h1><?= htmlspecialchars($status ?? 'Error', ENT_QUOTES, 'UTF-8') ?></h1>
<p><?= htmlspecialchars($message ?? 'Something went wrong.', ENT_QUOTES, 'UTF-8') ?></p>

<?= partial('footer') ?>