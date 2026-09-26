<?php

use yii\db\Migration;

/**
 * Class m251203_033707_migration_alter_table_rujukanbantaran_t
 */
class m251203_033707_migration_alter_table_rujukanbantaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE public.rujukanbantaran_t ADD COLUMN IF NOT EXISTS rencana_tindakan text;');
        $this->execute('ALTER TABLE public.rujukanbantaran_t ADD COLUMN IF NOT EXISTS tujuan_pemeriksaan text;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251203_033707_migration_alter_table_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251203_033707_migration_alter_table_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }
    */
}
