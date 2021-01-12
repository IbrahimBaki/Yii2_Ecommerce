<?php

/** @var \common\models\Order $order */
$orderAddress = $order->orderAddress;
?>

Account Information

    Firstname:     <?php echo $order->firstname ?>
    Lastname:      <?php echo $order->lastname ?>
    Email:         <?php echo $order->email ?>


Address Information

    Address:       <?php echo $orderAddress->address ?>
    City:          <?php echo $orderAddress->city ?>
    State:         <?php echo $orderAddress->state ?>
    Country:       <?php echo $orderAddress->country ?>
    Zipcode:	   <?php echo $orderAddress->zipcode ?>


Products
    Name        Quantity        Price

<?php foreach ($order->orderItems as $orderItem): ?>
    <?php echo $orderItem->product_name ?>      <?php echo $orderItem->quantity ?>      <?php echo Yii::$app->formatter->asCurrency($orderItem->quantity * $orderItem->unit_price) ?>
<?php endforeach;?>

Total Items : <?php echo $order->getItemsQuantity() ?>
Total Price : <?php echo Yii::$app->formatter->asCurrency($order->total_price) ?>
