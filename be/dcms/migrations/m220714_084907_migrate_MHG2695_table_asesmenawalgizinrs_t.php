<?php

use yii\db\Migration;

/**
 * Class m220714_084907_migrate_MHG2695_table_asesmenawalgizinrs_t
 */
class m220714_084907_migrate_MHG2695_table_asesmenawalgizinrs_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenawalgizinrs_t ADD IF NOT EXISTS is_anak BOOLEAN DEFAULT FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220714_084907_migrate_MHG2695_table_asesmenawalgizinrs_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220714_084907_migrate_MHG2695_table_asesmenawalgizinrs_t cannot be reverted.\n";

        return false;
    }
    */
}
