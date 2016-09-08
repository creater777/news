<?php
use yii\bootstrap\Html;
?>
<div>
    <h1>
        Новая новость
    </h1>
    <p><h3><?= $model->dateInner ?></h3></p>
    <p><h4><?= Html::encode($model->subj) ?></h4></p>
<p><?= Yii::$app->getFormatter()->format($model->post, 'ntext') ?></p>
</div>
