<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "events".
 *
 * @property integer $id
 * @property string $calssName
 * @property string $eventName
 * @property string $handlerClass
 * @property string $handlerMethod
 */
class Events extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'events';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calssName', 'eventName', 'handlerClass', 'handlerMethod'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'calssName' => 'Calss Name',
            'eventName' => 'Event Name',
            'handlerClass' => 'Handler Class',
            'handlerMethod' => 'Handler Method',
        ];
    }
}
