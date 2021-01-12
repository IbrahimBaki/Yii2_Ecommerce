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
    'id'=>'checkout-form',
    'action'=>['']
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
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr data-id="<?php echo $item['id'] ?>" data-url="<?php echo \yii\helpers\Url::to(['/cart/change-quantity'])?>">
                            <td>
                                <img src="<?php echo \common\models\Product::formatImageUrl($item['image']) ?>"
                                     style="width: 30px"
                                     alt="<?php echo $item['name'] ?>"
                                >
                            </td>
                            <td><?php echo $item['name'] ?></td>
                            <td><?php echo $item['quantity'] ?></td>
                            <td><?php echo Yii::$app->formatter->asCurrency($item['total_price']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <hr>
                <table class="table">
                    <tr>
                        <th>Total Items</th>
                        <td class="text-right"><?php echo $productQuantity ?></td>
                    </tr>
                    <tr>
                        <th>Total Price</th>
                        <td class="text-right">
                            <?php echo Yii::$app->formatter->asCurrency($totalPrice)?>
                        </td>
                    </tr>
                </table>
                <p class="text-right mt-3">
                    <button class="btn btn-secondary">Checkout</button>
                </p>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

