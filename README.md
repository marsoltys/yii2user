##NOTE: This is Yii2-User module, which is rewritten version of Yii-User which can be found on [http://www.yiiframework.com/extension/yii-user](http://www.yiiframework.com/extension/yii-user)


This module allows you to maintain users.

Yii-User Installation
=====================

Download
--------

Download or checkout (SVN/Git) from https://github.com/marsoltys/yii2user and unpack files in your protected/modules/user

Composer
---------

    composer require "marsoltys/yii2user:dev-master",

Configure
---------

Change your config web:

        'bootstrap' => [
            #...
            function ()
            {
                return Yii::$app->getModule('user');
            }
        ],

        #...
        'modules'=>[
            #...
            'user' => [
                'class' => 'marsoltys\yii2user\Module'
            ]
            #...
        ],

        #...
        // application components
        'components'=>[
            'user' => [
                'identityClass' => 'marsoltys\yii2user\models\User',
                'class' => 'marsoltys\yii2user\components\WebUser',
            ],
        ],
        #...
    
Change your config console:

    return array(
        #...
        'modules'=>[
            #...
            
            'user' => [
                'class' => 'mariusz_soltys\yii2user\Module'
            ],
            
            #...
        ]
        #...
    );

Install
------- 

Run console command:
    php yii migrate --migrationPath=@marsoltys/yii2user/migrations

Input admin login, email and password

##TODO 

- Fix "Autocomplete" and "Belongsto" components
- Refresh user session if anything changes within user details (logout user after pass change?)
- add activation email option when user is created by admin and status is not active