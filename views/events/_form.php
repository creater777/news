<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use kartik\depdrop\DepDrop;
use app\models\User;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Events */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="events-form">

    <?php
    $form = ActiveForm::begin(); 
    ?>

    <?php
    if ($model->isNewRecord){
        echo $form->field($model, 'name')->textInput(['maxlength' => true]);
    }
    echo $form->field($model, 'className')->dropDownList($model->getClassList(),['id' => 'className']);
    echo $form->field($model, 'eventName')->widget(DepDrop::classname(), [
        'data'=>$model->getEventList($model->className),
        'pluginOptions'=>[
            'depends'=>['className'],
            'placeholder'=>'',
            'url'=>Url::to(['events/geteventlist'])
        ]
    ]);
    ?>

    <?= $form->field($model, 'handlerClass')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'handlerMethod')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'toRole')->dropDownList(array_merge(['' => null], User::getRoleList())) ?>

    <?= $form->field($model, 'messagePattern')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php 
    ActiveForm::end(); 
$this->registerJs("
    $(\"#events-classname\").trigger('change.yii');
");
    
    ?>

</div>
