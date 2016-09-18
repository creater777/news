<?php

namespace app\models;

use Yii;
use yii\base\Component;
use app\models\News;
/**
 * Description of EventsInit
 *
 * @author SIR
 */
class EventsInit extends Component{
    public function init(){
        Yii::$app->on(News::EVENT_NEWS_UPDATE,function($event){
            Yii::error($event);
        });
        parent::init();
    }
}
