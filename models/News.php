<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property integer $createat
 * @property integer $updateat
 * @property string $subj
 * @property string $date
 * @property string $post
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
    
    public static function findLatest($time, $limit){
        //return static::findAll(['updateat < :date', 'params' => [':date' => $time]]);
        return static::find("updateat > `:time`", [':time' => $time])->orderBy('date')->limit($limit)->all();
    }
    
    public function setDateInner($value){
        return $this->date = $value ? strtotime($value) : null;
    }

    public function getDateInner(){
        return $this->date ? date("d.m.Y", $this->date) : '';
    }

    public function beforeSave($insert) {
        parent::beforeSave($insert);
        if ($insert){
            $this->createat = time();
        }
        if (!isset($this->date)){
            $this->date = time();
        }
        $this->updateat = time();
        return true;
    }
    
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        return true;
    }
}
