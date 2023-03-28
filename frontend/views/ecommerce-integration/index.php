<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\EcommerceIntegration;
use common\models\EcommercePlatform;

/* @var $this View */
/* @var $models EcommerceIntegration[] */

$title = 'E-commerce Integrations';
$this->title = $title . ' - ' . Yii::$app->name;
$this->params['breadcrumbs'][] = $title;
?>

<div>
    <h1>
        <?= Html::encode($title) ?>
    </h1>

    <div class="well well-sm">
        <?php if (EcommercePlatform::getShopifyObject()->isActive()) { ?>
            <a href="<?= Url::to(['/ecommerce-integration/shopify']) ?>" class="btn btn-success">Connect Shopify shop</a>
        <?php } ?>
    </div>

    <div>
        <?php if ($models) { ?>
            <?php foreach ($models as $model) { ?>
                <?= $this->render('_integration', [
                    'model' => $model,
                ]) ?>
            <?php } ?>
        <?php } else { ?>
            <div class="text-center">
                <h3>
                    No connected shops yet.
                    Please connect your first shop.
                </h3>
            </div>
        <?php } ?>
    </div>
</div>
