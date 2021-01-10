<?php


use common\models\User;
use yii\bootstrap4\ActiveForm;
use yii\web\View;


/** @var User $user */
/** @var View $this */
?>

<?php $form = ActiveForm::begin([
    'action'=>['/profile/update-account'],
    'options'=>[
        'data-pjax'=>1,
    ]
]); ?>

<?php if(isset($success) && $success): ?>
    <div class="alert alert-success">
        Your Account was Successfully Updated
    </div>
<?php endif; ?>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($user, 'firstname')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($user, 'lastname')->textInput(['autofocus' => true]) ?>
    </div>
</div>

<?= $form->field($user, 'username')->textInput(['autofocus' => true]) ?>

<?= $form->field($user, 'email') ?>
<div class="row">
    <div class="col"><?= $form->field($user, 'password')->passwordInput() ?></div>
    <div class="col"><?= $form->field($user, 'passwordConfirm')->passwordInput() ?></div>
</div>

<button class="btn btn-primary">Update</button>

<?php ActiveForm::end(); ?>

