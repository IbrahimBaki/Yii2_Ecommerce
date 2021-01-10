<?php

use common\models\User;
use common\models\UserAddress;
use yii\web\View;
use yii\widgets\Pjax;

/** @var User $user */
/** @var View $this */
/** @var UserAddress $userAddress */

?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                Address Information
            </div>
            <div class="card-body">
                  <?php Pjax::begin([
                    'enablePushState' => false,
                ]) ;
                 echo $this->render('user_address', [
                    'userAddress' => $userAddress,
                ]);
                Pjax::end() ?>

            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                Account Information
            </div>
            <div class="card-body">

                <?php Pjax::begin([
                    'enablePushState' => false,
                ]) ;
                     echo $this->render('user_account', [
                        'user' => $user,
                    ]) ;
                 Pjax::end() ?>
            </div>
        </div>
    </div>
</div>
