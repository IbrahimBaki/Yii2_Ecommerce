<?php

namespace common\models\query;

use common\models\CartItem;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\CartItem]].
 *
 * @see \common\models\CartItem
 */
class CartItemQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CartItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CartItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
/**
 * @param $userId
 * @return CartItemQuery
 */
    public function userId($userId)
    {
        return $this->andWhere(['created_by'=>$userId]);
    }
/**
 * @param $productId
 * @return CartItemQuery
 */
    public function productId($productId)
    {
        return $this->andWhere(['product_id'=>$productId]);
    }
}
