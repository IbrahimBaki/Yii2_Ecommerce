<?php


function isGuest(){
    return Yii::$app->user->isGuest;
}
function currentUserId(){
    return Yii::$app->user->id;
}

function param($key){

    return Yii::$app->params[$key];
}
