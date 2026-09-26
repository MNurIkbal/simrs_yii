<?php

use yii\db\Migration;

/**
 * Class m240507_105608_migrate_RPP1318_lookuptransaksi_m
 */
class m240507_105608_migrate_RPP1318_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'required_referral';");
        $this->execute("INSERT INTO public.lookuptransaksi_m
        (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
        VALUES('required_referral', 1, 'Required Field Referral Untuk Pendaftaran. TRUE = required, additional_value selain itu tidak wajib', 'FALSE', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240507_105608_migrate_RPP1318_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240507_105608_migrate_RPP1318_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
