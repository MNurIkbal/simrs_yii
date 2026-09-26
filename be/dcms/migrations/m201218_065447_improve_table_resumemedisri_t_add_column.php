<?php

use yii\db\Migration;

/**
 * Class m201218_065447_improve_table_resumemedisri_t_add_column
 */
class m201218_065447_improve_table_resumemedisri_t_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE resumemedisri_t ADD IF NOT EXISTS keluhan_utama TEXT; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS berat_badan VARCHAR(30); ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS tinggi_badan VARCHAR(30); ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS nadi VARCHAR(30); ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS rr VARCHAR(50); ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS td VARCHAR(50); ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS suhu VARCHAR(50); ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS skala VARCHAR(50); ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS alergi_obat TEXT; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS obat_diberikan TEXT; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS riwayat_penyakit_dahulu TEXT; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS metod_asmennyeri TEXT; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS tindakan JSON; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS order_laboratorium JSON; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS order_radiologi JSON; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS konsul JSON; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS obat JSON; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS doktor_rawat_bersama VARCHAR(100); ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS indikasi_pasien_dirawat TEXT; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS obat_dibawa_pulang JSON; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS instruksi TEXT; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS diag_awal TEXT; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS alergi_makanan TEXT; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS alergi_lainnya TEXT; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS reaksi_alergi_obat TEXT; ');
        $this->execute('ALTER  TABLE resumemedisri_t ADD IF NOT EXISTS kondisi_pulang TEXT; ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201218_065447_improve_table_resumemedisri_t_add_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201218_065447_improve_table_resumemedisri_t_add_column cannot be reverted.\n";

        return false;
    }
    */
}
