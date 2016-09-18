<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\RegisterForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
?>
<div class="users-form">
    <?php $form = ActiveForm::begin(['action' => Url::toRoute(['pswchange', 'id' => $model->id])]); ?>
    
    <?= $form->field($model, 'passwordInner')->passwordInput() ?>

    <?= $form->field($model, 'passwordVisual2')->passwordInput() ?>

    <div class="form-group">
        <?= Html::submitButton(
                $model->isNewRecord ? 'Задать пароль' : 'Сменить пароль', 
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            ) 
        ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
