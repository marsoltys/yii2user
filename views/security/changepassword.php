<?php

use marsoltys\yii2user\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title=Yii::$app->name . ' - '.Module::t("Change password");
$this->params['breadcrumbs']= [
    ['label' => Module::t("Profile"), 'url' => ['/user/profile']],
    Module::t("Change password"),
];
$menu= [
    ['label'=>Module::t('List User'), 'url'=> ['/user']],
    ['label'=>Module::t('Profile'), 'url'=> ['/user/profile']],
    ['label'=>Module::t('Edit'), 'url'=> ['/user/profile/edit']],
    ['label'=>Module::t('Logout'), 'url'=> ['/user/logout']],
];

if (Module::isAdmin()) {
    array_unshift($menu, ['label'=>Module::t('Manage Users'), 'url'=> ['/user/admin']]);
}

Module::getInstance()->setMenu($menu);
?>

<div class="change-password">

    <h1><?php echo Module::t("Change password"); ?></h1>

    <p class="note"><?php echo Module::t('Fields with <span class="required">*</span> are required.'); ?></p>

    <div class="col-lg-4">

        <?php $form = ActiveForm::begin([
            'id'=>'changepassword-form',
            'enableAjaxValidation'=>true,
            'validateOnSubmit'=>true,
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                //  'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
                'labelOptions' => ['class' => 'control-label'],
            ],
        ]); ?>

        <?php echo $form->errorSummary($model); ?>

        <?= $form->field($model, 'oldPassword')->passwordInput(); ?>

        <?= $form->field($model, 'password')->passwordInput()
            ->hint(Module::t("Minimal password length 4 symbols.")); ?>

        <?= $form->field($model, 'verifyPassword')->passwordInput(); ?>

        <div class="form-group">
            <?=
            Html::submitButton(
                Module::t("Save"),
                ['class' => 'btn btn-primary']
            ); ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div><!-- form -->