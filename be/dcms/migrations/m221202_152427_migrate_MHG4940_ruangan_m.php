<?php

use yii\db\Migration;

/**
 * Class m221202_152427_migrate_MHG4940_ruangan_m
 */
class m221202_152427_migrate_MHG4940_ruangan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.ruangan_m ADD IF NOT EXISTS ruangan_farmasi_id int4 NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221202_152427_migrate_MHG4940_ruangan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221202_152427_migrate_MHG4940_ruangan_m cannot be reverted.\n";

        return false;
    }
    */
}
