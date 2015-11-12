<?php

namespace mariusz_soltys\yii2user\controllers;

use mariusz_soltys\yii2user\models\User;
use mariusz_soltys\yii2user\models\UserChangePassword;
use mariusz_soltys\yii2user\Module;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class ProfileController extends Controller
{
    public $defaultAction = 'profile';

    /** @var \mariusz_soltys\yii2user\models\Profile the currently loaded data model instance. */
    private $model;

    /**
     * Shows a particular user profile.
     */
    public function actionProfile()
    {
        $model = $this->loadUser();
        return  $this->render('profile', [
            'model'=>$model,
            'profile'=>$model->profile,
        ]);
    }


    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionEdit()
    {
        $model = $this->loadUser();
        $profile=$model->profile;
        $post = Yii::$app->request->post();

        if (Yii::$app->request->isAjax && $model->load($post) && $profile->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load($post) && $profile->load($post)) {
            if ($model->save()&&$profile->save()) {
                Yii::$app->user->setFlash('success', Module::t("Changes has been saved"));
                $this->redirect(array('/user/profile'));
            } else {
                $profile->validate();
            }
        }

        return $this->render('edit', [
            'model'=>$model,
            'profile'=>$profile,
        ]);
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @return \mariusz_soltys\yii2user\models\User
     */
    public function loadUser()
    {
        if ($this->model===null) {
            if (Yii::$app->user->id) {
                $this->model = Module::getInstance()->user();
            }
            if ($this->model===null) {
                $this->redirect(Module::getInstance()->loginUrl);
            }
        }
        return $this->model;
    }
}
