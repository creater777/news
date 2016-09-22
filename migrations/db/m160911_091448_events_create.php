<?php

use yii\db\Schema;
use yii\db\Migration;

class m160911_091448_events_create extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function Up()
    {
        $tableOptions = 'ENGINE=InnoDB';
        $transaction=$this->db->beginTransaction();
        try{
            $this->createTable('events',[
               'id'=> $this->primaryKey(11),
               'createat'=> $this->integer(11)->notNull(),
               'updateat'=> $this->integer(11)->notNull(),
               'name'=> $this->string(255)->notNull(),
               'className'=> $this->string(255)->notNull(),
               'eventName'=> $this->string(255)->notNull(),
               'toRole'=> $this->string(50)->null()->defaultValue(null),
               'handlerClass'=> $this->string(255)->notNull(),
               'handlerMethod'=> $this->string(255)->notNull(),
               'messagePattern'=> $this->text()->null()->defaultValue(null),
            ], $tableOptions);
            $transaction->commit();
        } catch (Exception $e) {
             echo 'Catch Exception '.$e->getMessage().' and rollBack this';
             $transaction->rollBack();
        }
    }

    public function Down()
    {
        $transaction=$this->db->beginTransaction();
        try{
            $this->dropTable('events');
            $transaction->commit();
        } catch (Exception $e) {
            echo 'Catch Exception '.$e->getMessage().' and rollBack this';
            $transaction->rollBack();
        }
    }
}
