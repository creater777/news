<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "authors".
 *
 * @property integer $id
 * @property string $firstname
 * @property string $lastname
 * @property string $author_name
 *
 * @property Books $books
 */
class Authors extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'authors';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname'], 'required'],
            [['firstname', 'lastname'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Имя',
            'lastname' => 'Фамилия',
            'author_name' => 'ФИО',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function get($id)
    {
        return (new AuthorsQuery(get_called_class()))->where(['id' => $id])->one();
    }

    /**
     * @inheritdoc
     * @return AuthorsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AuthorsQuery(get_called_class());
    }
}
