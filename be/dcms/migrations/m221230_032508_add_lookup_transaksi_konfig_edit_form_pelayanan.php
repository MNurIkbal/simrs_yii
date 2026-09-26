<?php

use yii\db\Migration;

/**
 * Class m221230_032508_add_lookup_transaksi_konfig_edit_form_pelayanan
 */
class m221230_032508_add_lookup_transaksi_konfig_edit_form_pelayanan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi,additional_value,kode_nama,kode_singkatan) VALUES
        ('konfig_edit_form_pelayanan',0,'Konfigurasi tetap bisa melakukan edit setelah pasien dipulangkan, form yang memakai ini untuk sekarang form asmed, form askep dan form resume medis','true',NULL,NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221230_032508_add_lookup_transaksi_konfig_edit_form_pelayanan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221230_032508_add_lookup_transaksi_konfig_edit_form_pelayanan cannot be reverted.\n";

        return false;
    }
    */
}
