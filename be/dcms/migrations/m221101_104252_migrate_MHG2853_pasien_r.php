<?php

use yii\db\Migration;

/**
 * Class m221101_104252_migrate_MHG2853_pasien_r
 */
class m221101_104252_migrate_MHG2853_pasien_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.pasien_r RENAME COLUMN nama_bin TO nama_panggilan;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221101_104252_migrate_MHG2853_pasien_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221101_104252_migrate_MHG2853_pasien_r cannot be reverted.\n";

        return false;
    }
    */
}
