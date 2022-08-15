<?php
/**
 * - Removed username as input
 */

/**
 * @var yii\widgets\ActiveForm $form
 * @var frontend\models\User $user
 */
?>

<?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'password')->passwordInput() ?>
<?php
$array = \yii\helpers\ArrayHelper::map(
    \frontend\models\Customer::find()->all(), 'id', 'name'
);
echo $form->field($user, 'customer_id')->dropDownList($array, ['prompt'=> 'Select']) ?>
