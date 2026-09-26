<?php

use yii\db\Migration;

/**
 * Class m230727_074852_migration_glbj_176_add_column_default_depo_on_ruangan_m
 */
class m230727_074852_migration_glbj_176_add_column_default_depo_on_ruangan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.ruangan_m ADD IF NOT EXISTS ruangan_default_depo_id int4 NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230727_074852_migration_glbj_176_add_column_default_depo_on_ruangan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230727_074852_migration_glbj_176_add_column_default_depo_on_ruangan_m cannot be reverted.\n";

        return false;
    }
    */
}
