<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m181219_072441_alter_antrian_add_column_antrianasal_id
 */
class m181219_072441_alter_antrian_add_column_antrianasal_id extends Migration
{
    public $table_name = 'antrian_t';
    public $column_name = 'antrianasal_id';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $check = $this->getDb()->getSchema()->getTableSchema($this->table_name)->getColumn($this->column_name);
        if(!$check) {
            // Column doesn't exist
            $this->addColumn($this->table_name, $this->column_name, Schema::TYPE_INTEGER);
            $this->addCommentOnColumn($this->table_name, $this->column_name, 'mengambil antrian_id pasangan');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Fetch the table schema
        $check = $this->getDb()->getSchema()->getTableSchema($this->table_name)->getColumn($this->column_name);
        if($check) {
            // Column doesn't exist
            $this->dropColumn($this->table_name, $this->column_name);
        }
        // echo "m181219_072441_alter_antrian_add_column_antrianasal_id cannot be reverted.\n";

        // return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181219_072441_alter_antrian_add_column_antrianasal_id cannot be reverted.\n";

        return false;
    }
    */
}
