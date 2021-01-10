<?php


namespace frontend\base;


use common\models\CartItem;
use Yii;

class Controller extends \yii\web\Controller
{

    public function beforeAction($action)
    {
        $userId = Yii::$app->user->id;
        $this->view->params['cartItemCount'] = CartItem::findBySql("
        SELECT SUM(quantity) FROM cart_items WHERE created_by = :userId",['userId'=>$userId])->scalar();
        return parent::beforeAction($action);
    }

}