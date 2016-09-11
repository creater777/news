<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use app\models\News;
use app\models\NewsSearch;
use app\models\User;

/**
 * SiteController контроллер главной страницы, реализует действия над новостями
 * с контролем доступа
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
                        'actions' => ['index', 'error', 'latestnews'],
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

    /**
     * Начальная страница
     * @return генерируется список новостей
     */
    public function actionIndex()
    {
        $itemsInPage = News::getNewsInPage();
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $itemsInPage);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Просмотр новости
     * @param integer $id - идентификатор новости
     * @return генерируется просмотр
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Создание новости
     * @return генерируется форма
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
     * Обновление новости
     * @param integer $id - идентификатор
     * @return генерируется форма
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
     * Удаление новости
     * @param integer $id - идентификатор
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Получение списка последних новостей
     * используется jquery плагином для отображения на боковой панели
     * @param type $lasttime - время с которого необходимо получить
     * @param type $limit - максимальное значение
     * @return type - json объект со списком новостей
     */
    public function actionLatestnews($lasttime, $limit){
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->notificationonline){
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
     * Поиск новости по id
     * @param integer $id - идентификатор
     * @return объект News
     * @throws NotFoundHttpException если не найдено
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
