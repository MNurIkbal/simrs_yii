<?php

use yii\db\Migration;

/**
 * Class m200929_032910_migrate_mhkn_20200929_tabelpendaftaran
 */
class m200929_032910_migrate_mhkn_20200929_tabelpendaftaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute('ALTER TABLE "public"."pendaftaran_t" ADD COLUMN IF NOT EXISTS  "is_bsl" bool DEFAULT false;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200929_032910_migrate_mhkn_20200929_tabelpendaftaran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200929_032910_migrate_mhkn_20200929_tabelpendaftaran cannot be reverted.\n";

        return false;
    }
    */
}
