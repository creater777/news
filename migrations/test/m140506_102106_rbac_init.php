<?php
/**
 * Initializes RBAC tables
 */
class m140506_102106_rbac_init extends \yii\db\Migration
{
    public function init(){
        $this->db = 'dbtest';
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable('auth_rule', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
        ], $tableOptions);
        $this->batchInsert('auth_rule', ["name", "data", "created_at", "updated_at"],
        [
            [
                'name' => 'isMyProfile',
                'data' => 'O:24:"app\\controllers\\UserRule":3:{s:4:"name";s:11:"isMyProfile";s:9:"createdAt";i:1473687818;s:9:"updatedAt";i:1473687818;}',
                'created_at' => '1473687818',
                'updated_at' => '1473687818',
            ],
        ]);
                                
        $this->createTable('auth_item', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->integer()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
            'FOREIGN KEY (rule_name) REFERENCES ' . 'auth_rule' . ' (name)'.
                ' ON DELETE SET NULL ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createIndex('idx-auth_item-type', 'auth_item', 'type');
        $this->batchInsert('auth_item', ["name", "type", "description", "rule_name", "data", "created_at", "updated_at"],
        [
            [
                'name' => 'admin',
                'type' => '1',
                'description' => 'Администратор',
                'rule_name' => null,
                'data' => null,
                'created_at' => '1473687818',
                'updated_at' => '1473687818',
            ],
            [
                'name' => 'editNews',
                'type' => '2',
                'description' => 'Редактирование новостей',
                'rule_name' => null,
                'data' => null,
                'created_at' => '1473687818',
                'updated_at' => '1473687818',
            ],
            [
                'name' => 'editProfile',
                'type' => '2',
                'description' => 'Запрет на редактирование чужего профиля',
                'rule_name' => 'isMyProfile',
                'data' => null,
                'created_at' => '1473687818',
                'updated_at' => '1473687818',
            ],
            [
                'name' => 'moder',
                'type' => '1',
                'description' => 'Модератор',
                'rule_name' => null,
                'data' => null,
                'created_at' => '1473687818',
                'updated_at' => '1473687818',
            ],
            [
                'name' => 'user',
                'type' => '1',
                'description' => 'Пользователь',
                'rule_name' => null,
                'data' => null,
                'created_at' => '1473687818',
                'updated_at' => '1473687818',
            ],
            [
                'name' => 'userEdit',
                'type' => '2',
                'description' => 'Редактирование чужих профилей',
                'rule_name' => null,
                'data' => null,
                'created_at' => '1473687818',
                'updated_at' => '1473687818',
            ],
            [
                'name' => 'viewNews',
                'type' => '2',
                'description' => 'Просмотр новостей',
                'rule_name' => null,
                'data' => null,
                'created_at' => '1473687818',
                'updated_at' => '1473687818',
            ],
        ]);
        
        $this->createTable('auth_item_child', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY (parent, child)',
            'FOREIGN KEY (parent) REFERENCES ' . 'auth_item' . ' (name)'.
                ' ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child) REFERENCES ' . 'auth_item' . ' (name)'.
                ' ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->batchInsert('auth_item_child', ["parent", "child"],
        [
            [
                'parent' => 'admin',
                'child' => 'editNews',
            ],
            [
                'parent' => 'moder',
                'child' => 'editNews',
            ],
            [
                'parent' => 'user',
                'child' => 'editProfile',
            ],
            [
                'parent' => 'moder',
                'child' => 'user',
            ],
            [
                'parent' => 'admin',
                'child' => 'userEdit',
            ],
            [
                'parent' => 'admin',
                'child' => 'viewNews',
            ],
            [
                'parent' => 'user',
                'child' => 'viewNews',
            ],
        ]);
        
        $this->createTable('auth_assignment', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY (item_name, user_id)',
            'FOREIGN KEY (item_name) REFERENCES ' . 'auth_item' . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->batchInsert('auth_assignment', ["item_name", "user_id", "created_at"],
        [
            [
                'item_name' => 'admin',
                'user_id' => '1',
                'created_at' => '1473687818',
            ],
            [
                'item_name' => 'moder',
                'user_id' => '2',
                'created_at' => '1473687818',
            ],
            [
                'item_name' => 'user',
                'user_id' => '3',
                'created_at' => '1473687818',
            ],
        ]);      
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('auth_assignment');
        $this->dropTable('auth_item_child');
        $this->dropTable('auth_item');
        $this->dropTable('auth_rule');
    }
}