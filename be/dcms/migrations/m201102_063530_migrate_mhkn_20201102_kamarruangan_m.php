<?php

use yii\db\Migration;

/**
 * Class m201102_063530_migrate_mhkn_20201102_kamarruangan_m
 */
class m201102_063530_migrate_mhkn_20201102_kamarruangan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->execute('ALTER TABLE "public"."kamarruangan_m" ADD COLUMN IF NOT EXISTS"durasi" float8;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201102_063530_migrate_mhkn_20201102_kamarruangan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201102_063530_migrate_mhkn_20201102_kamarruangan_m cannot be reverted.\n";

        return false;
    }
    */
}
