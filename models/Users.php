<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property integer $active
 * @property string $email
 * @property integer $usergroupid
 * @property string $authKey
 * @property string $accessToken
 *
 * @property Usersgroup $usergroup
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'usergroupid', 'authKey', 'accessToken'], 'required'],
            [['active', 'usergroupid'], 'integer'],
            [['username', 'email'], 'string', 'max' => 255],
            [['password', 'authKey', 'accessToken'], 'string', 'max' => 40],
            [['username'], 'unique'],
            [['usergroupid'], 'exist', 'skipOnError' => true, 'targetClass' => Usersgroup::className(), 'targetAttribute' => ['usergroupid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'active' => 'Active',
            'email' => 'Email',
            'usergroupid' => 'Usergroupid',
            'authKey' => 'Auth Key',
            'accessToken' => 'Access Token',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsergroup()
    {
        return $this->hasOne(Usersgroup::className(), ['id' => 'usergroupid']);
    }

    /**
     * @inheritdoc
     * @return UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersQuery(get_called_class());
    }
}
