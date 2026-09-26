<?php

use yii\db\Migration;

/**
 * Class m221017_091558_migrate_seeder_modul_k
 */
class m221017_091558_migrate_seeder_modul_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE modul_k ADD IF NOT EXISTS url_page VARCHAR(100);
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/ambulan/informasi-permintaan-ambulan\' WHERE modul_key = \'ambulan\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/bedah/informasi-pasien-anestesi\' WHERE modul_key = \'anestesi\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/antrian\' WHERE modul_key = \'antrian\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/apotek/informasi-reseptur\' WHERE modul_key = \'apotek\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/asuhan-keperawatan/worklist\' WHERE modul_key = \'asuhan keperawatan\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/bedah/informasi-pasien-operasi\' WHERE modul_key = \'bedahsentral\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/dcms/menu-modul\' WHERE modul_key = \'dcms\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/fisioterapi/informasi-pasien-fisioterapi\' WHERE modul_key = \'fisioterapi\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/gizi/inf-permintaan-makan\' WHERE modul_key = \'gizi\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/igd/worklist\' WHERE modul_key = \'igd\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/jenazah/informasi-pasien-meninggal\' WHERE modul_key = \'jenazah\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/kasir/inf-pasien-pulang\' WHERE modul_key = \'kasir\';
        ');


        $this->execute('
            UPDATE modul_k SET url_page = \'/laboratorium/inf-pasien-rujukan-lab\' WHERE modul_key = \'laboratorium\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/mcu/informasi-pasien-mcu\' WHERE modul_key = \'mcu\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/master/profil-rumah-sakit/index\' WHERE modul_key = \'modul sysadmin\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/penatajasa/pencarian-pasien\' WHERE modul_key = \'penata jasa\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/pendaftaran/daftar\' WHERE modul_key = \'pendaftaran\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/pengadaan/info-purchase-order\' WHERE modul_key = \'pengadaan\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/penjamin-asuransi/informasi-pasien-non-bpjs\' WHERE modul_key = \'penjaminasuransi\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/persalinan/worklist\' WHERE modul_key = \'persalinan\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/radiologi/informasi-pasien-rad\' WHERE modul_key = \'radiologi\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/rajal/worklist\' WHERE modul_key = \'rajal\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/ranap/worklist\' WHERE modul_key = \'ranap\';
        ');

        $this->execute('
            UPDATE modul_k SET url_page = \'/pendaftaran/informasi-pencarian-pasien\' WHERE modul_key = \'rm\';
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221017_091558_migrate_seeder_modul_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221017_091558_migrate_seeder_modul_k cannot be reverted.\n";

        return false;
    }
    */
}
