<?php

/**
 * @var int $width
 * @var int $height
 * @var string $name
 * @var Yii\web\View $this
 */

use yii\helpers\Html;

$this->title = $name;

$this->registerCssFile('css/conway.css');
$this->registerJsFile('js/conway.js', ['position' => \Yii\web\View::POS_HEAD]);

?>
<div class="body-content">

    <table id="board"></table>

</div>

<?php

    $this->registerJs('
        setup();
        let conway;
        startFunc();
    ');

?>