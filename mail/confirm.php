<div>
    <p>
        Для завершения регистрации перейдите по ссылке
    </p>
    <?= Yii::$app->getUrlManager()->createAbsoluteUrl(['site/confirmemail', 'authKey' => $model->getAuthKey()]);?>
</div>
