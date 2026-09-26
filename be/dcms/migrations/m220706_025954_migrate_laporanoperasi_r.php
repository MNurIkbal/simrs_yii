<?php

use yii\db\Migration;

/**
 * Class m220706_025954_migrate_laporanoperasi_r
 */
class m220706_025954_migrate_laporanoperasi_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
                $this->execute('ALTER TABLE "public"."laporanoperasi_r" 
                                ADD COLUMN if not exists "dokter_id" int4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220706_025954_migrate_laporanoperasi_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220706_025954_migrate_laporanoperasi_r cannot be reverted.\n";

        return false;
    }
    */
}
