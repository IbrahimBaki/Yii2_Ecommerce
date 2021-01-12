<?php


namespace frontend\controllers;


use common\models\CartItem;
use common\models\Order;
use common\models\OrderAddress;
use common\models\Product;
use frontend\base\Controller;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CartController extends Controller
{

    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'only' => ['add', 'create-order', 'submit-payment'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,

                ],
            ],
            [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST', 'DELETE'],
                    'create-order' => ['POST'],
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        $cartItems = CartItem::getItemsForUser(currentUserId());

        return $this->render('index', [
            'items' => $cartItems,
        ]);

    }

    public function actionAdd()
    {
        $id = Yii::$app->request->post('id');
        $product = Product::find()->id($id)->published()->one();
        if (!$product) {
            throw new NotFoundHttpException("Product does not exist");
        }
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $found = false;
            foreach ($cartItems as &$item) {
                if ($item['id'] == $id) {
                    $item['quantity']++;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $cartItem = [
                    'id' => $id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'price' => $product->price,
                    'quantity' => 1,
                    'total_price' => $product->price,
                ];
                $cartItems[] = $cartItem;
            }

            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);

        } else {
            $userId = Yii::$app->user->id;
            $cartItem = CartItem::find()->userId($userId)->productId($id)->one();
            if ($cartItem) {
                $cartItem->quantity++;
            } else {
                $cartItem = new CartItem();
                $cartItem->product_id = $id;
                $cartItem->created_by = Yii::$app->user->id;
                $cartItem->quantity = 1;
            }
            if ($cartItem->save()) {
                return [
                    'success' => true
                ];
            } else {
                return [
                    'success' => false,
                    'errors' => $cartItem->errors,
                ];
            }
        }


    }

    public function actionDelete($id)
    {
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            foreach ($cartItems as $i => $cartItem) {
                if ($cartItem['id'] == $id) {
                    array_splice($cartItems, $i, 1);
                }
                break;
            }
            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
        } else {
            CartItem::deleteAll(['product_id' => $id, 'created_by' => currentUserId()]);
        }
        return $this->redirect(['index']);

    }

    public function actionChangeQuantity()
    {
        $id = Yii::$app->request->post('id');
        $product = Product::find()->id($id)->published()->one();
        if (!$product) {
            throw new NotFoundHttpException("Product does not exist");
        }
        $quantity = Yii::$app->request->post('quantity');
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            foreach ($cartItems as &$item) {
                if ($item['id'] == $id) {
                    $item['quantity'] = $quantity;
                    break;
                }
            }
            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
        } else {
            $cartItem = CartItem::find()->userId(currentUserId())->productId($id)->one();
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->save();

            }
        }
        return CartItem::getTotalQuantityForUser(currentUserId());
    }

    public function actionCheckout()
    {
        $cartItems = CartItem::getItemsForUser(currentUserId());
        $productQuantity = CartItem::getTotalQuantityForUser(currentUserId());
        $totalPrice = CartItem::getTotalPriceForUser(currentUserId());

        if (empty($cartItems)) {
            return $this->redirect([Yii::$app->homeUrl]);
        }
        //define new object from Order and OrderAddress ActiveRecords
        $order = new Order();

        $order->total_price = $totalPrice;
        $order->status = Order::STATUS_DRAFT;
        $order->created_at = time();
        $order->created_by = currentUserId();

        $transaction = Yii::$app->db->beginTransaction();
        //save order_id in OrderAddress table
        if ($order->load(Yii::$app->request->post())
            && $order->save()
            && $order->saveAddress(Yii::$app->request->post())
            && $order->saveOrderItems()) {
            $transaction->commit();

            //delete products from cart item after checkout
            CartItem::clearCartItems(currentUserId());

            return $this->render('pay-now', [
                'order' => $order,
            ]);
        }


        $orderAddress = new OrderAddress();
        if (!isGuest()) {
            //define user and get address using method in User
            $user = Yii::$app->user->identity;
            $userAddress = $user->getAddress();

            //filling order data from user table
            $order->firstname = $user->firstname;
            $order->lastname = $user->lastname;
            $order->email = $user->email;
            $order->status = Order::STATUS_DRAFT;

            //filling OrderAddress data from UserAddress
            $orderAddress->address = $userAddress->address;
            $orderAddress->city = $userAddress->city;
            $orderAddress->state = $userAddress->state;
            $orderAddress->country = $userAddress->country;
            $orderAddress->zipcode = $userAddress->zipcode;

            //get items from cart if not a guest
        }
        $cartItems = CartItem::getItemsForUser(currentUserId());


        return $this->render('checkout', [
            'order' => $order,
            'orderAddress' => $orderAddress,
            'cartItems' => $cartItems,
            'productQuantity' => $productQuantity,
            'totalPrice' => $totalPrice,
        ]);
    }

    public function actionSubmitPayment($orderId)
    {
        //if user is authorized => check that the order is belongs to current user
        $where = ['id' => $orderId, 'status' => Order::STATUS_DRAFT];
        if (!isGuest()) {
            $where['created_by'] = currentUserId();
        }
        //check if order exists or not
        $order = Order::findOne($where);
        if (!$order) { // if not exists
            throw new NotFoundHttpException();
        }

        // if order exists => get transactionId and status from ajax to save into order table
        $paypalOrderId = Yii::$app->request->post('orderId');
        $exists = Order::find()->andWhere(['paypal_order_id' => $paypalOrderId])->exists();
        if ($exists) {
            throw new BadRequestHttpException();
        }
        //first make a validation and then save the order //we use paypal/paypal-checkout-sdk package
        $environment = new SandboxEnvironment(Yii::$app->params['paypalClientId'], Yii::$app->params['paypalSecret']);
        $client = new PayPalHttpClient($environment);
        $response = $client->execute(new OrdersGetRequest($paypalOrderId));

        if ($response->statusCode === 200) {
            $order->paypal_order_id = $paypalOrderId;
            //make sure that user will not change payment value in inspect
            $paidAmount = 0;
            foreach ($response->result->purchase_units as $purchase_unit) {
                if ($purchase_unit->amount->currency_code === 'USD') {
                    $paidAmount += $purchase_unit->amount->value;
                }
            }
            if ($paidAmount == $order->total_price && $response->result->status === 'COMPLETED') {
                $order->status = Order::STATUS_COMPLETED;
            }
            $order->transaction_id = $response->result->purchase_units[0]->payments->captures[0]->id;
            if ($order->save()) {
                //if the order successfully made we will send emails to vendor and customer
                if (!$order->sendEmailToVendor()) {
                    Yii::error("Email to the vendor is not sent");
                }
                if (!$order->sendEmailToCustomer()) {
                    Yii::error("Email to the customer is not sent");
                }
                return [
                    'success' => true,
                ];
            }
        } else {
            Yii::error("Order was not saved. Data: " . VarDumper::dumpAsString($order->toArray()) .
                '.Errors: ' . VarDumper::dumpAsString($order->errors));
        }
        throw new BadRequestHttpException();

        //todo Validate the transaction ID. It must not be used and it must be valid transaction ID in paypal.
// we need to make api request using secret token of paypal api to get that information
//


    }
}