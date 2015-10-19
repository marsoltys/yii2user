<?php

namespace mariusz_soltys\yii2user;

use mariusz_soltys\yii2user\models\User;
use Yii;

class Module extends \yii\base\Module
{
    /**
     * @var int
     * @desc items on page
     */
    public $user_page_size = 10;

    /**
     * @var int
     * @desc items on page
     */
    public $fields_page_size = 10;

    /**
     * @var string
     * @desc hash method (md5,sha1 or algo hash function http://www.php.net/manual/en/function.hash.php)
     */
    public $hash='md5';

    /**
     * @var boolean
     * @desc use email for activation user account
     */
    public $sendActivationMail=true;

    /**
     * @var boolean
     * @desc allow auth for is not active user
     */
    public $loginNotActiv=false;

    /**
     * @var boolean
     * @desc activate user on registration (only $sendActivationMail = false)
     */
    public $activeAfterRegister=false;

    /**
     * @var boolean
     * @desc login after registration (need loginNotActiv or activeAfterRegister = true)
     */
    public $autoLogin=true;

    public $registrationUrl = array("/user/registration");
    public $recoveryUrl = array("/user/recovery/recovery");
    public $loginUrl = array("/user/login");
    public $logoutUrl = array("/user/logout");
    public $profileUrl = array("/user/profile");
    public $returnUrl = array("/user/profile");
    public $returnLogoutUrl = array("/user/login");

    public $captchaParams = array(
        'class'=>'CCaptchaAction',
        'backColor'=>0xFFFFFF,
        'foreColor'=>0x2040A0,
    );


    /**
     * @var int
     * @desc Remember Me Time (seconds), defalt = 2592000 (30 days)
     */
    public $rememberMeTime = 2592000; // 30 days

    public $fieldsMessage = '';

    /**
     * @var array
     * @desc Profile model relation from other models
     */
    public $profileRelations = array();

    /**
     * @var boolean
     */
    public $captcha = array('registration'=>true);

    /**
     * @var boolean
     */
    //public $cacheEnable = false;

    public $tableUsers = '{{users}}';
    public $tableProfiles = '{{profiles}}';
    public $tableProfileFields = '{{profiles_fields}}';

    public $defaultScope = array(
        'with'=>array('profile'),
    );

    static private $_user;
    static private $_users=array();
    static private $_userByName=array();
    static private $_admin;
    static private $_admins;

    /**
     * @var array
     * @desc Behaviors for models
     */
    public $componentBehaviors=array();

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'mariusz_soltys\yii2user\controllers';


    public function init()
    {
        parent::init();
        $this->setAliases([
            '@user-assets' => __DIR__ . '/views/assets',
        ]);
    }

    public function getBehaviorsFor($componentName){
        if (isset($this->componentBehaviors[$componentName])) {
            return $this->componentBehaviors[$componentName];
        } else {
            return array();
        }
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        // your custom code here
        //
        // return true; // or false to not run the action
    }


    /**
     * @param $str
     * @param $params
     * @param $dic
     * @return string
     */
    public static function t($str='',$params=array(),$dic='user') {
        if (Yii::t("Module", $str)==$str)
            return Yii::t("Module.".$dic, $str, $params);
        else
            return Yii::t("Module", $str, $params);
    }

    /**
     * @param string $string string to encrypt
     * @return string hash.
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function encrypting($string="") {

        return Yii::$app->getSecurity()->generatePasswordHash($string);
    }

    /**
     * @param $place
     * @return boolean
     */
    public static function doCaptcha($place = '') {
        if(!extension_loaded('gd'))
            return false;
        if (in_array($place, Yii::$app->getModule('user')->captcha))
            return Yii::$app->getModule('user')->captcha[$place];
        return false;
    }

    /**
     * Return admin status.
     * @return boolean
     */
    public static function isAdmin() {
        if(Yii::$app->user->isGuest)
            return false;
        else {
            if (!isset(self::$_admin)) {
                if(self::user()->superuser)
                    self::$_admin = true;
                else
                    self::$_admin = false;
            }
            return self::$_admin;
        }
    }

    /**
     * Return admins.
     * @return array superusers names
     */
    public static function getAdmins() {
        if (!self::$_admins) {
            $admins = User::model()->active()->superuser()->findAll();
            $return_name = array();
            foreach ($admins as $admin)
                array_push($return_name,$admin->username);
            self::$_admins = ($return_name)?$return_name:array('');
        }
        return self::$_admins;
    }

    /**
     * Send to user mail
     */
    public static function sendMail($email,$subject,$message) {
        $adminEmail = Yii::$app->params['adminEmail'];
        $headers = "MIME-Version: 1.0\r\nFrom: $adminEmail\r\nReply-To: $adminEmail\r\nContent-Type: text/html; charset=utf-8";
        $message = wordwrap($message, 70);
        $message = str_replace("\n.", "\n..", $message);
        return mail($email,'=?UTF-8?B?'.base64_encode($subject).'?=',$message,$headers);
    }

    /**
     * Send to user mail
     */
    public function sendMailToUser($user_id,$subject,$message,$from='') {
        $user = User::findOne($user_id);
        if (!$from) $from = Yii::$app->params['adminEmail'];
        $headers="From: ".$from."\r\nReply-To: ".Yii::$app->params['adminEmail'];
        return mail($user->email,'=?UTF-8?B?'.base64_encode($subject).'?=',$message,$headers);
    }

    /**
     * Return safe user data.
     * @param $id int user id not required
     * @return user object or false
     */
    public static function user($id=0,$clearCache=false) {
        if (!$id&&!Yii::$app->user->isGuest)
            $id = Yii::$app->user->id;
        if ($id) {
            if (!isset(self::$_users[$id])||$clearCache)
                self::$_users[$id] = User::findOne($id);
            return self::$_users[$id];
        } else return false;
    }

    /**
     * Return safe user data.
     * @param $username string user name
     * @return user object or false
     */
    public static function getUserByName($username) {
        if (!isset(self::$_userByName[$username])) {
            $_userByName[$username] = User::findOne(['username'=>$username]);
        }
        return $_userByName[$username];
    }

//	/**
//	 * Return safe user data.
//	 * @param user id not required
//	 * @return user object or false
//	 */
//	public function users() {
//		return User;
//	}
}