<?php

use yii\db\Migration;

/**
 * Class m210909_115017_improvment_penyebab_kematian_US1033_1034_1035
 */
class m210909_115017_improvment_penyebab_kematian_US1033_1034_1035 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS status_jenazah VARCHAR(50);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS tgl_kremasi TIMESTAMP(6);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS nama_pemeriksa_jenazah VARCHAR(100);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS kualifikasi_pemeriksa VARCHAR(50);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS waktu_pemeriksaan_jenazah TIMESTAMP(6);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS dasar_diagnosis    VARCHAR(50);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS kelompok_kematian VARCHAR(100);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS tempat_kematian    VARCHAR(100);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS penyebab_langsung VARCHAR(100);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS penyebab_antara    VARCHAR(100);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS penyebab_dasar VARCHAR(100);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS kondisi_lain VARCHAR(100);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS penyebab_utama_bayi VARCHAR(100);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS penyebab_utama_ibu VARCHAR(100);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS penyebab_lain_bayi VARCHAR(100);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS penyebab_lain_ibu VARCHAR(100);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS pihak_menerima VARCHAR(100);
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS hubungan_penerima VARCHAR(50);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210909_115017_improvment_penyebab_kematian_US1033_1034_1035 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210909_115017_improvment_penyebab_kematian_US1033_1034_1035 cannot be reverted.\n";

        return false;
    }
    */
}
