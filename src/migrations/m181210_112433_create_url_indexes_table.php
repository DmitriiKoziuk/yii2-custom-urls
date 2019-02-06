<?php

use yii\db\Migration;

/**
 * Handles the creation of table `url_indexes`.
 */
class m181210_112433_create_url_indexes_table extends Migration
{
    private $_urlIndexTableName = '{{%url_indexes}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->_urlIndexTableName, [
            'url'             => $this->string(500)->notNull(),
            'redirect_to_url' => $this->string(500)->null()->defaultValue(NULL),
            'module_name'     => $this->string(45)->defaultValue(NULL),
            'controller_name' => $this->string(45)->notNull(),
            'action_name'     => $this->string(45)->notNull(),
            'entity_id'       => $this->string(45)->notNull(),
            'created_at'      => $this->integer()->unsigned()->notNull(),
            'updated_at'      => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey(
            'primary-key',
            $this->_urlIndexTableName,
            'url'
        );

        $this->createIndex(
            'idx-url_indexes-entity',
            $this->_urlIndexTableName,
            [
                'module_name',
                'controller_name',
                'action_name',
                'entity_id',
            ],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->_urlIndexTableName);
    }
}
