<?php

use yii\db\Migration;

/**
 * Class m240215_041253_migrate_DSV_1104_fix_default_value_format_mcu
 */
class m240215_041253_migrate_DSV_1104_fix_default_value_format_mcu extends Migration
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
        VALUES('format_mcu', 0, 'Untuk format template MCU. Available value untuk additional_value: default, (default), dan prima (RS Prima)', 'default', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240215_041253_migrate_DSV_1104_fix_default_value_format_mcu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240215_041253_migrate_DSV_1104_fix_default_value_format_mcu cannot be reverted.\n";

        return false;
    }
    */
}
