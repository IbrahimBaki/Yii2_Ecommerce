<?php

namespace common\i18n;

use common\models\Order;
use yii\bootstrap4\Html;

/**
 * @package common\i18n
 */
class Formatter extends \yii\i18n\Formatter
{

    public function asOrderStatus($status)
    {
        if ($status === Order::STATUS_COMPLETED) {
            return Html::tag('span', 'Completed', ['class' => 'badge badge-success']);
        } else if ($status === Order::STATUS_PAID) {
            return Html::tag('span', 'Paid', ['class' => 'badge badge-primary']);
        } else if ($status === Order::STATUS_DRAFT) {
            return Html::tag('span', 'UnPaid', ['class' => 'badge badge-secondary']);
        } else {
            return Html::tag('span', 'Failed', ['class' => 'badge badge-danger']);
        }
    }

}