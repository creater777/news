<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
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
    
    public function setDateInner($value){
        return $this->date = $value ? strtotime($value) : null;
    }

    public function getDateInner(){
        return $this->date ? date("d.m.Y", $this->date) : '';
    }

}
