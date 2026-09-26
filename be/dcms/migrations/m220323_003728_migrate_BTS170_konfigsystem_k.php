<?php

use yii\db\Migration;

/**
 * Class m220323_003728_migrate_BTS170_konfigsystem_k
 */
class m220323_003728_migrate_BTS170_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
          ADD COLUMN IF NOT EXISTS "is_baca_pasiensedangrawat" bool DEFAULT true;
        ');

        $this->execute('COMMENT ON COLUMN "public"."konfigsystem_k"."is_baca_pasiensedangrawat" IS \'Kebutuhan Sensus Bulanan Ranap Pasien Awal Ketika cutoff menjadikan pasien rawat saat ini menjadi pasien awal\';
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220323_003728_migrate_BTS170_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220323_003728_migrate_BTS170_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
