<?php
namespace app\models;

use yii\base\Event;

/**
 * Description of ARProvider
 *
 * @author SIR
 */
class ARProvider extends \yii\db\ActiveRecord {

    /**
     * Перед сохранением пользователя устанавливаем дату создания и обновления
     * генерируем новый код активации
     * @param type $insert
     * @return boolean
     */
    public function beforeSave($insert) {
        if ($insert){
            $this->createat = time();
        }
        $this->updateat = time();
        return parent::beforeSave($insert);
    }
    
    /**
     * После сохранения рассылаем администраторам оповещение по email при создании.
     * Отправляем пользователю оповещение при смене пароля
     * @param type $insert
     * @param type $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        if (Yii::$app->id == 'basic-console'){
            return true;
        }
        if ($insert){
            RegisterForm::sendConfirm($this);
            $admins = Yii::$app->authManager->getUserIdsByRole(self::ROLE_ADMIN);
            foreach ($admins as $id){
                RegisterForm::sendNewUser($this->findIdentity($id));
            }
        }
        if(!$insert && isset($changedAttributes['password'])){
            RegisterForm::sendPswChanged($this);
        }
        return true;
    }
    
    /**
     * После удаления пользователя, удаляем из модели доступа
     */
    public function afterDelete() {
        parent::afterDelete();
        Yii::$app->authManager->revokeAll($this->getId());
    }
}
