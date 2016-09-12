<?php

use yii\db\Schema;
use yii\db\Migration;

class m160911_091448_base extends Migration
{

    public function init()
    {
        $this->db = 'dbtest';
        parent::init();
    }

    public function Up()
    {
        $tableOptions = 'ENGINE=InnoDB';
        $transaction=$this->db->beginTransaction();
        try{
            $this->createTable('news',[
               'id'=> $this->primaryKey(11),
               'createat'=> $this->integer(11)->notNull(),
               'updateat'=> $this->integer(11)->notNull(),
               'subj'=> $this->string(512)->null()->defaultValue(null),
               'date'=> $this->integer(11)->null()->defaultValue(null),
               'post'=> $this->text()->null()->defaultValue(null),
            ], $tableOptions);

            $this->createTable('users',[
               'id'=> $this->primaryKey(11),
               'createat'=> $this->integer(11)->notNull(),
               'updateat'=> $this->integer(11)->notNull(),
               'username'=> $this->string(255)->notNull(),
               'password'=> $this->string(40)->notNull(),
               'active'=> $this->integer(1)->null()->defaultValue(0),
               'email'=> $this->string(255)->null()->defaultValue(null),
               'notificationonline'=> $this->integer(1)->notNull()->defaultValue(0),
               'notificationemail'=> $this->integer(1)->notNull()->defaultValue(0),
               'authkey'=> $this->string(40)->notNull(),
               'authkeyexpired'=> $this->integer(11)->notNull(),
               'accessToken'=> $this->string(40)->notNull(),
            ], $tableOptions);
            $this->createIndex('username','{{%users}}','username',false);

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
            $this->dropTable('news');
            $this->dropTable('users');
            $transaction->commit();
        } catch (Exception $e) {
            echo 'Catch Exception '.$e->getMessage().' and rollBack this';
            $transaction->rollBack();
        }
    }
}
