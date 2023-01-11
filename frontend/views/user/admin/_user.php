<?php
/**
 * - Removed username as input
 */

/**
 * @var yii\widgets\ActiveForm $form
 * @var frontend\models\User $user
 */
$customerList = \yii\helpers\ArrayHelper::map(\frontend\models\Customer::find()->all(), 'id', 'name');
?>

<?= $form->field($user, 'email')->textInput(['maxlength' => 255]); ?>
<?= $form->field($user, 'password')->passwordInput(); ?>
<?= $form->field($user, 'customer_id')->dropDownList($customerList, ['prompt'=> 'Select']); ?>
<?= $form->field($user, 'type')->dropDownList($user->getTypes(), ['prompt'=> 'Select']); ?>
