<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'dateCreateInner',
            [
                'label' => 'Активный',
                'value' => $model->isActive() ? 'Да':'Нет',
            ],
            [
                'label' => 'Оповещать о новых новостях на сайте',
                'value' => $model->notificationonline ? 'Да':'Нет',
            ],
            [
                'label' => 'Оповещать о новых новостях по email',
                'value' => $model->notificationemail ? 'Да':'Нет',
            ],
            [
                'label' => 'Роль',
                'value' => implode(', ', $model->role),
            ],
            'email:email',
        ],
    ]) ?>

</div>
