<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserForm;
use app\models\UsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;

/**
 * UsersController контроллер, реализующий действия над пользователями
 */
class UsersController extends Controller
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
                        'actions' => ['update', 'view', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'delete', 'create'],
                        'allow' => true,
                        'roles' => [User::ROLE_ADMIN],
                    ],
                ],
            ],            
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
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
        ];
    }
    
    /**
     * Отображение списка пользователей
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Просмотр параметров пользователя
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if (!\Yii::$app->user->can(User::PERMISSION_EDITPROFILE, ['users' => $model]) &&
                !\Yii::$app->user->can(User::ROLE_ADMIN)) {
            throw new ForbiddenHttpException(Yii::t('yii','You are not allowed to perform this action.'));
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Форма для создания пользователя
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserForm();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Форма редактирования пользователей
     * Для не администраторов доступно только для своего профиля
     * @param integer $id - идентификатор
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!\Yii::$app->user->can(User::PERMISSION_EDITPROFILE, ['users' => $model]) &&
                !\Yii::$app->user->can(User::ROLE_ADMIN)) {
            throw new ForbiddenHttpException(Yii::t('yii','You are not allowed to perform this action.'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Удаление пользователя
     * @param integer $id - идентификатор
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Поиск пользователя
     * @param integer $id
     * @return Объект User
     * @throws NotFoundHttpException если не найден
     */
    protected function findModel($id)
    {
        if (($model = UserForm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
