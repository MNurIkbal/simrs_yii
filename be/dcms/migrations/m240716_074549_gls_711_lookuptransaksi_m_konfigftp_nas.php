<?php

use yii\db\Migration;

/**
 * Class m240716_074549_gls_711_lookuptransaksi_m_konfigftp_nas
 */
class m240716_074549_gls_711_lookuptransaksi_m_konfigftp_nas extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.lookuptransaksi_m
        WHERE kode_transaksi='konfigftp_nas';");
        
        $this->execute("INSERT INTO public.lookuptransaksi_m
        (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
        VALUES('konfigftp_nas', 0, 'Untuk konfig bridgin nas. Kolom kode_id berfungsi untuk konfig button berkas pasien, jika kode_id = 0 maka button berkas pasien disable dan sebaliknya. Kolom additional_value berfungsi untuk konfig FTP', '{\"host\":\"192.168.202.244\",\"username\":\"test\",\"password\":\"test123\",\"path\":\"/ftp/Rekam Medis\"}', 'Konfig FTP NAS', NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240716_074549_gls_711_lookuptransaksi_m_konfigftp_nas cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240716_074549_gls_711_lookuptransaksi_m_konfigftp_nas cannot be reverted.\n";

        return false;
    }
    */
}
