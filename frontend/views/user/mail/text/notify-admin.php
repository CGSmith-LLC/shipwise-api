<?php

/**
 * @var string $username
 * @var string $adminUrl
 */
?>
<?= Yii::t('user', 'Hello Admin') ?>,

<?= Yii::t('user', 'A new user, {user}, has just registered on {app}',
    ['user' => $username, 'app' => Yii::$app->name]) ?>.

<?php if (isset($adminUrl)): ?>
    <?= Yii::t('user', 'To open admin page click the link below') ?>.

    <?= $adminUrl ?>

    <?= Yii::t('user', 'If you cannot click the link, please try pasting the text into your browser') ?>.
<?php endif ?>
