<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Events */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="events-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'calssName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'eventName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'handlerClass')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'handlerMethod')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
