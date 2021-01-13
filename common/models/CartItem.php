<?php

namespace common\models;


use common\models\query\CartItemQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%cart_items}}".
 *
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property int|null $created_by
 *
 * @property User $createdBy
 * @property Product $product
 */
class CartItem extends ActiveRecord
{
    const SESSION_KEY = 'CART_ITEMS';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%cart_items}}';
    }

    public static function getTotalQuantityForUser($currentUserId)
    {
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $sum = 0;
            foreach ($cartItems as $cartItem) {
                $sum += $cartItem['quantity'];
            }
        } else {
            $sum = CartItem::findBySql("SELECT SUM(quantity) FROM cart_items WHERE created_by = :userId", ['userId' => $currentUserId])->scalar();
        }
        return $sum;
    }

    public static function getTotalPriceForUser($currentUserId)
    {
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $sum = 0;
            foreach ($cartItems as $cartItem) {
                $sum += $cartItem['quantity'] * $cartItem['price'];
            }
        } else {
            $sum = CartItem::findBySql(
                "SELECT SUM(c.quantity * p.price) 
                    FROM cart_items c
                    LEFT JOIN products p on c.product_id = p.id
                    WHERE c.created_by = :userId", ['userId' => $currentUserId])->scalar();
        }
        return $sum;
    }

    public static function getTotalPriceForItemForUser($productId,$currentUserId)
    {
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $sum = 0;
            foreach ($cartItems as $cartItem) {
                if($cartItem['id'] == $productId)
                $sum += $cartItem['quantity'] * $cartItem['price'];
            }
        } else {
            $sum = CartItem::findBySql(
                "SELECT SUM(c.quantity * p.price) 
                    FROM cart_items c
                    LEFT JOIN products p on c.product_id = p.id
                    WHERE c.product_id= :id AND c.created_by = :userId", ['id'=>$productId,'userId' => $currentUserId])->scalar();
        }
        return $sum;
    }

    public static function getItemsForUser($currentUserId)
    {
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
        } else {
            $cartItems = CartItem::findBySql(
                "SELECT 
                    c.product_id as id,
                    p.image,
                    p.name, 
                    p.price,
                    c.quantity,
                    p.price * c.quantity as total_price
                FROM cart_items c 
                LEFT JOIN products p on p.id = c.product_id 
                WHERE c.created_by = :userId",
                ['userId' => $currentUserId])
                ->asArray()
                ->all();
        }
        return $cartItems;
    }

    public static function clearCartItems($currentUserId)
    {
        if(isGuest()){
            Yii::$app->session->remove(CartItem::SESSION_KEY);
        }else{
            CartItem::deleteAll(['created_by'=>$currentUserId]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity'], 'required'],
            [['product_id', 'quantity', 'created_by'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'quantity' => 'Quantity',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\User
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ProductQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * {@inheritdoc}
     * @return CartItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CartItemQuery(get_called_class());
    }

}
