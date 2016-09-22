<?php

namespace app\components;

use Yii;
use yii\base\Component;
use app\models\News;
use app\models\Events;
use yii\base\Event;
use yii\db\AfterSaveEvent;
/**
 * Description of EventsInit
 *
 * @author SIR
 */
class EventsInit extends Component{

    /**
     * Подключение событий к моделям
     */
    public function init(){
        $eventObjects = Events::find()->all();
        
        foreach($eventObjects as $eventObject){
            if (!class_exists($eventObject->className)){
                Yii::warning("Class $eventObject->calssName does not exist");
                continue;
            }
            
            Event::on($eventObject->className,  $eventObject->eventName, function($event){
                $class = $event->data->handlerClass;
                $method = $event->data->handlerMethod;
                if (class_exists($class) && method_exists($class, $method)){
                    $class::$method($event);
                } else {
                    Yii::warning("Method $method in $class does not exist");
                }
            }, $eventObject);
        }
        parent::init();
    }
}
