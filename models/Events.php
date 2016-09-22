<?php

namespace app\models;

use Yii;
use app as Classes;

/**
 * This is the model class for table "events".
 *
 * @property integer $id
 * @property string $className
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
            [['name', 'className', 'eventName', 'handlerClass', 'handlerMethod'], 'required'],
            [['name', 'className', 'eventName', 'handlerClass', 'handlerMethod'], 'string', 'max' => 255],
            [['toRole'], 'string', 'max' => 50],
            [['messagePattern'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
            'className' => 'Полный путь к классу на который навешивается событие',
            'eventName' => 'Имя события',
            'toRole' => 'Группа пользователей получатели',
            'handlerClass' => 'Полный путь к классу обработчику событий',
            'handlerMethod' => 'Метод обработчика событий',
            'messagePattern' => 'Передаваемое сообщение',
        ];
    }
    
    /*
     * Получение списка констант событий
     * @param $className - имя класса в котором происходит поиск
     * @return array - массив для DepDrop widget
     *    [
     *       ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
     *       ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
     *    ]
     */
    public static function getEvents($className){
        if (!class_exists($className)){
            return [];
        }
        $class = new \ReflectionClass($className);
        $eventConstants=[];
        foreach($class->getConstants() as $key => $value){
            if (substr($key, 0, 5) == 'EVENT'){
                //$eventConstants[$value] = $key;
                $eventConstants[] = ['id' => $value, 'name' => $key];
            }
        }
        return $eventConstants;
    }

    /*
     * Получение списка констант событий
     * @param $className - имя класса в котором происходит поиск
     * @return array - массив для DepDrop widget
     *    [
     *       ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
     *       ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
     *    ]
     */
    public static function getEventList($className){
        if (!class_exists($className)){
            return [];
        }
        $class = new \ReflectionClass($className);
        $eventConstants=[];
        foreach($class->getConstants() as $key => $value){
            if (substr($key, 0, 5) == 'EVENT'){
                $eventConstants[$value] = $key;
            }
        }
        return $eventConstants;
    }
    
    /*
     * Список зарегистрированных классов в namespace \app и \yii
     * @return array
     */
    public static function getClassList(){
        $classes = get_declared_classes();
        $result = [];
        foreach ($classes as $value){
            if (substr($value, 0, 3) != 'app' &&
                    substr($value, 0, 3) != 'yii'){
                continue;
            }
            $result[$value] = $value;
        }
        natsort($result);
        return $result;
    }
}