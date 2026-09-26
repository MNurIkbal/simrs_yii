<?php

use yii\db\Migration;

/**
 * Class m240212_085051_migrate_mcu_prima_lookuptransaksi_m
 */
class m240212_085051_migrate_mcu_prima_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.lookuptransaksi_m
        WHERE kode_transaksi='format_mcu' AND kode_id=0;");

        $this->execute("INSERT INTO public.lookuptransaksi_m
        (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
        VALUES('format_mcu', 0, 'Untuk format template MCU. Available value untuk additional_value: NULL (default), default, (default), dan prima (RS Prima)', 'prima', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240212_085051_migrate_mcu_prima_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240212_085051_migrate_mcu_prima_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
