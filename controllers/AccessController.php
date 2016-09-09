<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\ConfirmForm;
use app\models\User;

class AccessController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'login', 'logout', 'error', 'register', 'confirmemail', 'captcha', 'resendemail'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionRegister()
    {
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            return $this -> render('registermsg', [
                'message' => "Вам высланы инструкции для продолжения регистрации. Проверьте вашу почту",
            ]);
        }
        return $this->render('register', [
            'model' => $model,
        ]);
    }
    
    public function actionConfirmemail($authKey){
        $user = User::findByAuthKey($authKey);
        if (!isset($user)){
            throw new NotFoundHttpException("Пользователь не найден");
        }

        if (!$user->validateAuthKey($authKey)){
            $url = Yii::$app->getUrlManager()->createAbsoluteUrl(['site/resendemail', 'authKey' => $authKey]);
            return $this -> render('registermsg', [
                'message' => 'Время подтверждения истекло. <a href = "'.$url.'" >Выслать повторно</a>',
            ]);
        }

        $model = new ConfirmForm();
        if ($model->load(Yii::$app->request->post()) && $model->activate($user)) {
            return $this -> render('registermsg', [
                'message' => "Регистрация прошла успешно",
            ]);
        }

        return $this->render('confirm', [
            'model' => $model,
        ]);
    }
    
    public function actionResendemail($authKey){
        $user = User::findByAuthKey($authKey);
        if ($user){
            $user->generateAuthKey(Yii::$app->params['authKeyExpired']);
            $user->update(false);
            RegisterForm::sendConfirm($user);
            $msg = "Письмо с инструкцией по активации высланно на " . $user->email;
        } else{
            $msg = "Пользователь не найден";
        }
        return $this -> render('registermsg', [
            'message' => $msg,
        ]);
    }
    
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->goHome();
    }
}
