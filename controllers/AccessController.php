<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\PasswordForm;
use app\models\User;

/**
 * Контроллер, реализующий механизмы регистрации и авторизации
 */
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

    /**
     * Действие авторизации
     * @return рендерит форму логина
     */
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

    /**
     * Действие регистрации
     * @return рендерит форму регистрации
     */
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
    
    /**
     * Действие подттверждение авторизации по email
     * @param type $authKey - ключ авторизации пользователя
     * @return type рендерит форму ввода пароля пользователя
     * @throws NotFoundHttpException
     */
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

        $model = new PasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->activate($user)) {
            Yii::$app->user->login($user);
            $this->goHome();
        }

        return $this->render('confirm', [
            'model' => $model,
        ]);
    }
    
    /**
     * Действие на переотправку пароля
     * @param type $authKey - ключ авторизации который был присвоен пользователю
     * @return type
     */
    public function actionResendemail($authKey){
        $user = User::findByAuthKey($authKey);
        if (!isset($user)){
            throw new NotFoundHttpException("Пользователь не найден");
        }
        $user->generateAuthKey(Yii::$app->params['authKeyExpired']);
        $user->update(false);
        RegisterForm::sendConfirm($user);
        return $this -> render('registermsg', [
            'message' => "Письмо с инструкцией по активации высланно на " . $user->email,
        ]);
    }
    
    /**
     * Выхот из системы
     * @return type
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Страницы индекса нет, переходим на главную
     * как вариант можно NotFoundHttpException поставить
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect(['access/login']);
    }
}
