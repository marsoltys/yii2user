<?php

namespace marsoltys\yii2user\models;

use marsoltys\yii2user\Module;
use Yii;
use yii\base\Model;

/**
 * UserChangePassword class.
 * UserChangePassword is the data structure for keeping
 * user change password form data. It is used by the 'changepassword' action of 'UserController'.
 */
class UserChangePassword extends Model
{
    public $oldPassword;
    public $password;
    public $verifyPassword;

    public function rules()
    {
        return Yii::$app->controller->action->id == 'recovery' ? [
            [['password', 'verifyPassword'], 'required'],
            [['password', 'verifyPassword'],
                'string',
                'max'=>128,
                'min' => 4,
                'message' => Module::t("Incorrect password (minimal length 4 symbols).")],
            ['verifyPassword',
                'compare',
                'compareAttribute'=>'password',
                'message' => Module::t("Retype Password is incorrect.")],
        ] : [
            [['oldPassword', 'password', 'verifyPassword'], 'required'],
            [['oldPassword', 'password', 'verifyPassword'],
                'string',
                'max'=>128,
                'min' => 4,
                'message' => Module::t("Incorrect password (minimal length 4 symbols).")],
            ['verifyPassword',
                'compare',
                'compareAttribute'=>'password',
                'message' => Module::t("Retyped Password is incorrect.")],
            ['oldPassword', 'verifyOldPassword'],
        ];
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return [
            'oldPassword' => Module::t("Old Password"),
            'password' => Module::t("Password"),
            'verifyPassword' => Module::t("Retype Password"),
        ];
    }

    /**
     * Verify Old Password
     */
    public function verifyOldPassword($attribute, $params)
    {
        $current = User::find()->notsafe()->findByPk(Yii::$app->user->id)->one()->password;
        $cond = Yii::$app->security->validatePassword($this->$attribute, $current);
        if (!$cond) {
            $this->addError($attribute, Module::t("Old Password is incorrect."));
        }
    }
}
