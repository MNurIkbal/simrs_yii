<?php

use yii\db\Migration;

/**
 * Class m230919_003010_migrate_RPP605_table_carabayar_m
 */
class m230919_003010_migrate_RPP605_table_carabayar_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE carabayar_m ADD IF NOT EXISTS "penjamindefault_id" int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230919_003010_migrate_RPP605_table_carabayar_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230919_003010_migrate_RPP605_table_carabayar_m cannot be reverted.\n";

        return false;
    }
    */
}
