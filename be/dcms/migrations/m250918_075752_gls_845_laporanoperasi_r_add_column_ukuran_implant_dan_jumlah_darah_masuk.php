<?php

use yii\db\Migration;

/**
 * Class m250918_075752_gls_845_laporanoperasi_r_add_column_ukuran_implant_dan_jumlah_darah_masuk
 */
class m250918_075752_gls_845_laporanoperasi_r_add_column_ukuran_implant_dan_jumlah_darah_masuk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."laporanoperasi_r" 
                      ADD COLUMN IF NOT EXISTS "ukuran_implant" varchar(255) COLLATE "pg_catalog"."default",
                      ADD COLUMN IF NOT EXISTS "jumlah_darah_masuk" varchar(255) COLLATE "pg_catalog"."default";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250918_075752_gls_845_laporanoperasi_r_add_column_ukuran_implant_dan_jumlah_darah_masuk cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250918_075752_gls_845_laporanoperasi_r_add_column_ukuran_implant_dan_jumlah_darah_masuk cannot be reverted.\n";

        return false;
    }
    */
}
