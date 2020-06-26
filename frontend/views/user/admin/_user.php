<?php
/**
 * - Removed username as input
 */

/**
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\models\User $user
 */
?>

<?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'password')->passwordInput() ?>
<?php
$array = \yii\helpers\ArrayHelper::map(
    \frontend\models\Customer::find()->all(), 'id', 'name'
);
array_unshift($array, "Select");
echo $form->field($user, 'customer_id')->dropDownList($array) ?>
