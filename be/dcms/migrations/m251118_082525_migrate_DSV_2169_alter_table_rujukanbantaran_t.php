<?php

use yii\db\Migration;

/**
 * Class m251118_082525_migrate_DSV_2169_alter_table_rujukanbantaran_t
 */
class m251118_082525_migrate_DSV_2169_alter_table_rujukanbantaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE public.rujukanbantaran_t ADD COLUMN IF NOT EXISTS pasien_lama bool DEFAULT FALSE;');

        $this->execute('ALTER TABLE public.rujukanbantaran_t ADD COLUMN IF NOT EXISTS no_telp varchar(20) NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251118_082525_migrate_DSV_2169_alter_table_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251118_082525_migrate_DSV_2169_alter_table_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }
    */
}
