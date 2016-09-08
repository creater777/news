<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

use app\models\News;
use app\models\NewsSearch;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\User;

/**
 * SiteController implements the CRUD actions for News model.
 */
class SiteController extends Controller
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
                        'actions' => ['index', 'login', 'error', 'register', 'confirmemail', 'captcha', 'resendemail','latestnews'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['view', 'logout'],
                        'allow' => true,
                        'roles' => [User::PERMISSION_VIEWNEWS],
                    ],
                    [
                        'actions' => ['create', 'update'],
                        'allow' => true,
                        'roles' => [User::PERMISSION_EDITNEWS],
                    ],
                    [
                        'actions' => ['?'],
                        'allow' => true,
                        'allow' => [User::ROLE_ADMIN],
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
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionRegister()
    {
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            return $this->goHome();
        }
        return $this->render('register', [
            'model' => $model,
        ]);
    }
    
    public function actionConfirmemail($authKey){
        $user = User::findByAuthKey($authKey);
        if (!isset($user)){
            $msg =  "Пользователь не найден";
        } elseif ($user->validateAuthKey($authKey)){
            $user->activateUser();
            $user->update(false);
            $msg = "Проверка email прошла успешно.".$user->username;
        } else{
            $url = Yii::$app->getUrlManager()->createAbsoluteUrl(['site/resendemail', 'authKey' => $authKey]);
            $msg = 'Время подтверждения истекло. <a href = "'.$url.'" >Выслать повторно</a>';
        }
        return $this -> render('confirmEmail', [
            'message' => $msg,
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
        return $this -> render('confirmEmail', [
            'message' => $msg,
        ]);
    }
    
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single News model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionLatestnews($lasttime, $limit){
        if (!Yii::$app->user->identity->notificationonline){
            return json_encode('stop');
        }
        $posts = [];
        foreach (News::findLatest($lasttime, $limit) as $news){
            $item['id'] = $news->id;
            $item['updateat'] = $news->updateat;
            $item['date'] = $news->date;
            $item['subj'] = $news->subj;
            $posts[] = $item;
        }
        return json_encode($posts);
    }
    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
