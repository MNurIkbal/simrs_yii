<?php

use yii\db\Migration;

/**
 * Class m231128_074211_migrate_DSV828_table_diagnosa_m
 */
class m231128_074211_migrate_DSV828_table_diagnosa_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
           ALTER TABLE diagnosa_m ADD IF NOT EXISTS validcode int2 DEFAULT 0; 
        ");

        $this->execute("
            ALTER TABLE diagnosa_m ADD IF NOT EXISTS ina_grouper VARCHAR(5) DEFAULT 'UNU';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231128_074211_migrate_DSV828_table_diagnosa_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231128_074211_migrate_DSV828_table_diagnosa_m cannot be reverted.\n";

        return false;
    }
    */
}
