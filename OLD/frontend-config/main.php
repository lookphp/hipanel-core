<?php

/*
 * HiPanel core package
 *
 * @link      https://hipanel.com/
 * @package   hipanel-core
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2016, HiQDev (http://hiqdev.com/)
 */

$params = array_merge(
    require(Yii::getAlias('@hipanel/common/config/params.php')),
    require(Yii::getAlias('@project/common/config/params.php')),
    require(Yii::getAlias('@project/common/config/params-local.php')),
    require(Yii::getAlias('@hipanel/frontend/config/params.php')),
    require(Yii::getAlias('@project/frontend/config/params.php')),
    require(Yii::getAlias('@project/frontend/config/params-local.php'))
);

return [
    'id'                  => 'hipanel',
    'name'                => 'HiPanel',
    'basePath'            => dirname(__DIR__),
    'runtimePath'         => '@project/frontend/runtime',
    'bootstrap'           => ['log', 'pluginManager'],
    'defaultRoute'        => 'site',
    'controllerNamespace' => 'frontend\controllers',
    'language'            => 'en-US',
    'sourceLanguage'      => 'en-US',
    'components'          => [
        'request' => [
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'response' => [
            'class' => 'hipanel\base\Response',
        ],
        'user' => [
            'class'           => 'hipanel\base\User',
            'identityClass'   => 'common\models\User',
            'enableAutoLogin' => true,
            'seller'          => $params['user.seller'],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                'default' => [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                'merchant' => [
                    'class'      => 'yii\log\FileTarget',
                    'logFile'    => '@runtime/logs/merchant.log',
                    'categories' => ['merchant'],
                ],
                'email' => [
                    'class' => 'hipanel\log\EmailTarget',
                    'levels' => ['error'],
                    'message' => [
                        'from' => 'hipanel@hiqdev.com',
                        'to' => 'logs@hiqdev.com',
                        'subject' => 'HiPanel error log',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'authClientCollection' => [
            'class'   => 'hiam\authclient\Collection',
            'clients' => [
                'hiam' => [
                    'class'        => 'hiam\authclient\HiamClient',
                    'site'         => $params['hiam_site'],
                    'clientId'     => $params['hiam_client_id'],
                    'clientSecret' => $params['hiam_client_secret'],
                ],
            ],
        ],
        'urlManager' => [
            'class' => 'hipanel\base\LanguageUrlManager',
            'languages' => [
                'en' => 'en-US',
                'ru' => 'ru-RU',
            ],
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'enableStrictParsing' => false,
            'rules'           => [
                '<_c:[\w\-]+>/<id:\d+>'              => '<_c>/view',
                '<_c:[\w\-]+>'                       => '<_c>/index',
                '<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_c>/<_a>',
                'file/<id:\d+>/<name:\S{1,128}>'     => 'file/view',
            ],
        ],
        'view' => [
            'class' => 'hipanel\base\View',
        ],
        'formatter' => [
            'locale'      => 'ru-RU',
            'nullDisplay' => '&nbsp;',
            'sizeFormatBase' => 1000,
        ],
        'pluginManager' => [
            'class' => 'hiqdev\pluginmanager\PluginManager',
        ],
        'themeManager' => [
            'class'  => 'hiqdev\thememanager\ThemeManager',
            'assets' => [
                'hipanel\frontend\assets\AppAsset',
            ],
        ],
        'menuManager' => [
            'class' => 'hiqdev\menumanager\MenuManager',
            'items' => [
                'sidebar' => [
                    'items' => [
//                        'header' => [
//                            'label'     => 'MAIN NAVIGATION',
//                            'options'   => ['class' => 'header'],
//                        ],
                    ],
                ],
                'breadcrumbs' => [
                    'saveToView' => 'breadcrumbs',
                ],
            ],
        ],
    ],
    'modules' => [
        'gridview' => [
            'class' => 'kartik\grid\Module',
        ],
        'markdown' => [
            'class' => 'kartik\markdown\Module',
        ],
        'setting'  => [
            'class' => 'app\modules\setting\Module',
        ],
    ],
    'params' => $params,
];
