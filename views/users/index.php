<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'username',
            [
                'label' => 'Активный',
                'format' => 'text',
                'value' => function($data){
                     return $data->isActive() ? 'Да' : 'Нет';
                }
            ],
            'role' =>
            [
                'label' => 'Роль',
                'format' => 'text',
                'value' => function($data){
                     return $data->getRoleList()[$data->getRole()];
                }
            ],
            'email:email',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
