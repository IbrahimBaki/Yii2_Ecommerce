<?php


function isGuest(){
    return Yii::$app->user->isGuest;
}
function currentUserId(){
    return Yii::$app->user->id;
}
