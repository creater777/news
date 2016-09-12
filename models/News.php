<?php

namespace app\models;

use Yii;
use app\models\User;
/**
 * News - модель новостей, реализует методы доступа и модель поведения
 *
 * @property integer $id - идентификатор
 * @property integer $createat - время создания
 * @property integer $updateat - время обновления
 * @property string $subj - заголовок новости
 * @property string $date - дата новости
 * @property string $post - текст новости
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['dateInner', 'date', 'format' => 'php:d.m.Y'],
            [['post'], 'string'],
            [['subj'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subj' => 'Заголовок',
            'date' => 'Дата',
            'dateInner' => 'Дата',
            'post' => 'Содержание',
        ];
    }
    
    /**
     * Поиск последних новостей по дате обновления
     * @param type $time - время ограничивающее выборку
     * @param type $limit - максимальное значение выборки
     * @return type array News - массив новостей
     */
    public static function findLatest($time, $limit){
        return static::find()->andWhere('updateat > ' . strval($time))->orderBy('date')->limit($limit)->all();
    }
    
    /**
     * Геттеры и сеттеры виртуального поля DateInner, 
     * предназначенный для визуального отображения даты новости (поле date)
     * @param type string $value - строковое значение времени
     * @return type int - значение времени по Unix
     */
    public function setDateInner($value){
        return $this->date = $value ? strtotime($value) : null;
    }

    /**
     * @return type string - строковое значение времени
     */
    public function getDateInner(){
        return $this->date ? date("d.m.Y", $this->date) : '';
    }

    /**
     * Геттеры и сеттеры виртуального поля newsInPage
     * предназначенного для хранения соответствующего поля в куках
     * используется в настройках отображения колличества новостей на странице
     * @return type int - значение newsInPage из куков
     */
    public static function getNewsInPage(){
        //$cookies = Yii::$app->request->cookies;
        return isset($_COOKIE['newsInPage']) ? $_COOKIE['newsInPage'] : null;// $cookies->getValue('newsInPage', 3);
    }
    
    /**
     * Установка newsInPage в куки
     * @param type $value - значение newsInPage
     */
    public static function setNewsInPage($value){
        $cookies = Yii::$app->response->cookies;
        $cookies->add(new \yii\web\Cookie([
            'name' => 'newsInPage',
            'value' => $value,
        ]));
    }
    
    /**
     * Действие перед сохранение новости
     * при добавление устанавливается значение createat,
     * updateat при любом сохранении
     * @param type $insert
     * @return boolean
     */
    public function beforeSave($insert) {
        parent::beforeSave($insert);
        if ($insert){
            $this->createat = time();
        }
        if (!isset($this->date) || $this->date ==0){
            $this->date = time();
        }
        $this->updateat = time();
        return true;
    }
    
    /**
     * Действие после сохранения
     * Происходит рассылка всем зарегистрированным пользователям добавленная новость,
     * у которых стоит соответствующая настройка
     * @param type $insert
     * @param type $changedAttributes
     * @return boolean
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        if ($insert){
            $users = User::findAllActual();
            foreach($users as $user){
                if (!$user->notificationemail ||
                        empty($user->email)){
                    continue;
                }
                Yii::$app->mailer->compose('newnews', ['model' => $this])
                    ->setTo([$user->email => $user->username])
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setSubject('Новая новость')
                    ->send();            
            }
        }
        return true;
    }
}
