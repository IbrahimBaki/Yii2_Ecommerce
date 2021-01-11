<?php

/**
 * @var \common\models\Order $order
 * @var  \common\models\OrderAddress $orderAddress
 * @var array $cartItems
 * @var int $productQuantity
 * @var float $totalPrice
 */

use yii\bootstrap4\ActiveForm;

?>
<?php $form = ActiveForm::begin([
    'action'=>[''],
]); ?>

<div class="row">
    <div class="col">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Account Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($order, 'firstname')->textInput(['autofocus' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($order, 'lastname')->textInput(['autofocus' => true]) ?>
                    </div>
                </div>
                <?= $form->field($order, 'email') ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5>Address Information</h5>
            </div>
            <div class="card-body">

                <?= $form->field($orderAddress, 'address') ?>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($orderAddress, 'city') ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($orderAddress, 'state') ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($orderAddress, 'country') ?>
                    </div>
                </div>
                <?= $form->field($orderAddress, 'zipcode') ?>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5>Order Summary</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td ><?php echo $productQuantity ?> Product</td>
                    </tr>
                    <tr>
                        <td>Total Price</td>
                        <td class="text-right">
                            <?php echo Yii::$app->formatter->asCurrency($totalPrice)?>
                        </td>
                    </tr>
                </table>

                <p class="text-right mt-3">
                    <button class="btn btn-secondary">Continue</button>
                </p>
            </div>
        </div>
    </div>
</div>


<?php ActiveForm::end(); ?>