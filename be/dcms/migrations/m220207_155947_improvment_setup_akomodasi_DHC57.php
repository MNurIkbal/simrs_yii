<?php

use yii\db\Migration;

/**
 * Class m220207_155947_improvment_setup_akomodasi_DHC57
 */
class m220207_155947_improvment_setup_akomodasi_DHC57 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'konfig_stop_akomodasi\';
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'konfig_stop_akomodasi\', 0, \'[CutOff , Grace Period, HalfCharge\', \'["12:00", 2, 6]\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220207_155947_improvment_setup_akomodasi_DHC57 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220207_155947_improvment_setup_akomodasi_DHC57 cannot be reverted.\n";

        return false;
    }
    */
}
