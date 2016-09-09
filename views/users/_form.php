<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $model->isNewRecord ? $form->field($model, 'username'):'' ?>
    
    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?php
    if (Yii::$app->user->can(User::PERMISSION_USEREDIT))
        echo $form->field($model, 'active')->checkbox();
    ?>

    <?= $form->field($model, 'notificationonline')->checkbox() ?>

    <?= $form->field($model, 'notificationemail')->checkbox() ?>

    <?php 
    if (Yii::$app->user->can(User::PERMISSION_USEREDIT)){
        $roles = [User::ROLE_ADMIN => User::ROLE_ADMIN,
                  User::ROLE_MODERATOR => User::ROLE_MODERATOR,
                  User::ROLE_USER => User::ROLE_USER,
            ];
        $form->field($model, 'role')->listBox($roles);
    }
    ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
