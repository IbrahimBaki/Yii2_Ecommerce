<?php


use common\models\UserAddress;
use yii\bootstrap4\ActiveForm;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var UserAddress $userAddress */
?>

<?php Pjax::begin([
        'enablePushState' => false,
]) ?>
<?php if(isset($success) && $success): ?>
<div class="alert alert-success">
    Your Address was Successfully Updated
</div>
<?php endif; ?>
<?php $addressForm = ActiveForm::begin([
    'action' => ['/profile/update-address'],
    'options'=>[
        'data-pjax'=>1
    ],
]); ?>
<?= $addressForm->field($userAddress, 'address') ?>
<?= $addressForm->field($userAddress, 'city') ?>
<?= $addressForm->field($userAddress, 'state') ?>
<?= $addressForm->field($userAddress, 'country') ?>
<?= $addressForm->field($userAddress, 'zipcode') ?>
<button class="btn btn-primary">Update</button>
<?php ActiveForm::end(); ?>
<?php Pjax::end() ?>
