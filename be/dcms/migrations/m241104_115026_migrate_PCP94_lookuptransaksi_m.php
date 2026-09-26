<?php

use yii\db\Migration;

/**
 * Class m241104_115026_migrate_PCP94_lookuptransaksi_m
 */
class m241104_115026_migrate_PCP94_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'konfig_approve_fisio';");
        $this->execute("INSERT INTO public.lookuptransaksi_m
(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
VALUES('konfig_approve_fisio', 1, 'Konfigurasi untuk approval perubahan program fisioterapi', 'false', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241104_115026_migrate_PCP94_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241104_115026_migrate_PCP94_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
