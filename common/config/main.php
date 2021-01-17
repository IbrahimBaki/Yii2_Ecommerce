<?php
require_once __DIR__.'/../../common/helpers.php';


use common\i18n\Formatter;

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter'=>[
            'class'=> Formatter::class,
            'datetimeFormat'=>'php:d/m/Y H:i',
        ],
    ],
];
