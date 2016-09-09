<div>
    <p>
        Для завершения регистрации перейдите по ссылке
    </p>
    <?= Yii::$app->getUrlManager()->createAbsoluteUrl(['access/confirmemail', 'authKey' => $model->getAuthKey()]);?>
</div>
