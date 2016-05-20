<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
date_default_timezone_set('Europe/Amsterdam');
Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap');
require_once( dirname(__FILE__) . '/../components/Helpers.php'); //load helpers
return [
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'Documentatie Centrum Urk',
    'language'=>'nl',
    'theme'=>'dcu',
    // preloading 'log' component
    'preload'=>[
        'log',
    ],

    // autoloading model and component classes
    'import'=>[
        'application.models.*',
        'application.components.*',
    ],


    'aliases' => [
        'xupload' => 'ext.xupload'
    ],

    'modules'=>[
        'gii'=>[
            'class'=>'system.gii.GiiModule',
            'password'=>'baak10dr',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters'=>['127.0.0.1','::1', '92.68.44.153'],
            'generatorPaths'=>[
                'bootstrap.gii',
            ],
        ],
    ],

    // application components
    'components'=>[
        'user'=>[
            'class'=>'WebUser',
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
        ],
        'bootstrap'=>[
            'class'=>'bootstrap.components.Bootstrap',
        ],
        'phpThumb'=>[
            'class'=>'ext.EPhpThumb.EPhpThumb',
        ],
        'cache'=>[
            'class'=>'system.caching.CDbCache',
       ],
        'assetManager' => [
            'linkAssets' => true,
        ],
        'clientScript'=>[
            'coreScriptPosition' => CClientScript::POS_END,
        ],        
        // uncomment the following to enable URLs in path-format
        
        // 'urlManager'=>array(
        //     'urlFormat'=>'path',
        //     'rules'=>array(
        //         '<controller:\w+>/<id:\d+>'=>'<controller>/view',
        //         '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
        //         '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
        //     ),
        // ),

        'db'=>[require(__DIR__ . '/db.php'),
        ],
        
        'errorHandler'=>[
            // use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ],
        'log'=>[
            'class'=>'CLogRouter',
            'routes'=>[
                [
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ],
                // uncomment the following to show log messages on web pages
                
                [
                    'class'=>'CWebLogRoute',
                ],
                
            ],
       ],
    ],

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>[
        // this is used in contact page
        'adminEmail'=>'jpost@gbugrafici.nl',
        'cache_expiration'=>10,
        'twitter'=>[
            'consumer_key'=>'gZCkFUiJ6lTmTHilqcDEL2JO7',
            'consumer_secret'=>'S8fuf4NpKcHU0cIWazPBYLaEyD4SIQujuaKCrEZchPQr2mLNJJ',
            'oauth_token'=>'493165751-7M5DNbvjFaGIZpo9PBzSFiIwBZ7lN820i886gmlv',
            'oauth_secret'=>'xYHAMd55iyVC7tiIIa9EhejJTFnQYGLNdzMWzBZnbDuXW',
            'user'=>'St_Urker_taol',    
            'amount'=>'2'
        ],
    ],
    
];