<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = $model->subj;
$this->params['breadcrumbs'][] = ['label' => 'News', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
    <?php
        if (Yii::$app->user->can(User::PERMISSION_EDITNEWS)){
            echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
        }
        if (Yii::$app->user->can(User::ROLE_ADMIN)){
            echo Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]);
        }
    ?>
    </p>

    <?= DetailView::widget([
        'template' => '<tr><th></th><td>{value}</td></tr>',
        'model' => $model,
        'attributes' => [
            'dateInner',
            'post:ntext',
        ],
    ]) ?>

</div>
