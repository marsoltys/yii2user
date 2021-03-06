<?php

/* @var $this \yii\web\View */
use marsoltys\yii2user\assets\UserAssets;
use marsoltys\yii2user\Module;
use yii\bootstrap\Nav;

//UserAssets::register($this);

/* @var $content string */

UserAssets::register($this);

$this->beginContent('@marsoltys/yii2user/views/layouts/main.php'); ?>

    <div class="row">
        <div class="col-md-12">
            <?=
            Nav::widget([
                'items' => Module::getInstance()->getMenu(),
                'options' => ['class' =>'nav-pills'], // set this to nav-tab to get tab-styled navigation
            ]);
            ?>
        </div>
        <div class="col-md-12">
            <?= $content ?>
        </div>
    </div>
<?php $this->endContent();