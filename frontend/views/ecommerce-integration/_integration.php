<?php
use yii\web\View;
use common\models\EcommerceIntegration;
use common\models\EcommercePlatform;

/* @var $this View */
/* @var $model EcommerceIntegration */
?>

<?php
    switch ($model->ecommercePlatform->name) {
        case EcommercePlatform::SHOPIFY_PLATFORM_NAME:
            echo $this->render('_shopify', [
                'model' => $model,
            ]);
            break;
        default:
    }
?>
