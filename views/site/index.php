<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\User;
use app\models\News;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
    <?php
        if (Yii::$app->user->can(User::PERMISSION_EDITNEWS)){
            echo Html::a('Создать', ['create'], ['class' => 'btn btn-success']);
        }
    ?>
    </p>
    <?php 
        $items = ['2' => 2, '20' => 20, '40' => 40, '60' => 60];
        $itemIndex = News::getNewsInPage();
        echo 'Отображать по ' . Html::dropDownList('newsInPage', $itemIndex, $items) . ' записей';
 //       echo $this->render('_search', ['model' => $searchModel]);

        Pjax::begin(['id' => 'news']);
            $columns = [
                'dateInner',
                'subj',
            ];
            if (Yii::$app->user->can(User::PERMISSION_VIEWNEWS)){
                $template = '{view}';
            }
            if (Yii::$app->user->can(User::PERMISSION_EDITNEWS)){
                $template = '{update} {view}';
            }
            if (Yii::$app->user->can(User::ROLE_ADMIN)){
                $template = '{update} {view} {delete}';
            }
            if (isset($template)){
                $columns[] = [
                    'class' => 'yii\grid\ActionColumn',
                    'headerOptions' => ['width' => '80'],
                    'template' => $template,    
                ];
            }
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'showHeader' => false,
                'columns' => $columns,
            ]); 
        Pjax::end();   
    ?>
</div>
