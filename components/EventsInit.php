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
    
    private $classes = [];
    
    public function init(){
        $eventObjects = Events::find()->all();
        
        foreach($eventObjects as $eventObject){
            if (!class_exists($eventObject->calssName)){
                continue;
            }
            
            Event::on($eventObject->calssName,  $eventObject->eventName, function($event){
                $class = $eventObject->handlerClass;
                $method = $eventObject->handlerMethod;
                if (class_exists($class) && method_exists($class, $method)){
                    $class::$method($event);
                } else {
                    Yii::warning("Method $method in $class does not exist");
                    Yii::warning($eventObject);
                }
            });
        }
        parent::init();
    }
}
