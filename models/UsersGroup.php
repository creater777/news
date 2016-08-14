<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usersgroup".
 *
 * @property integer $id
 * @property string $groupname
 *
 * @property Users[] $users
 */
class UsersGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'usersgroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['groupname'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'groupname' => 'Groupname',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['usergroupid' => 'id']);
    }
    
    public static function getUserGroupId(){
        return 3;
    }
}
