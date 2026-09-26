<?php

use yii\db\Migration;

/**
 * Class m190326_071744_edit_cpptrj_v
 */
class m190326_071744_edit_cpptrj_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW cpptrj_v;
        ');
        $this->execute('
            CREATE OR REPLACE VIEW cpptrj_v AS 
             SELECT pendaftaran_t.pendaftaran_id,
                soaprj_t.ruangan_id,
                ruangan_m.ruangan_nama,
                soaprj_t.pegawai_id,
                pegawai_m.nama_pegawai,
                kelompokpegawai_m.kelompokpegawai_nama,
                soaprj_t.subject,
                soaprj_t.object,
                soaprj_t.a_diag_utama,
                soaprj_t.a_diag_penyerta,
                soaprj_t.planning,
                soaprj_t.tgl_soaprj,
                soaprj_t.td_diastolic,
                soaprj_t.td_systolic,
                soaprj_t.pernapasan,
                soaprj_t.beratbadan_kg,
                soaprj_t.tinggibadan_cm,
                soaprj_t.imt,
                soaprj_t.detaknadi,
                soaprj_t.suhutubuh,
                soaprj_t.is_nyeri,
                soaprj_t.skala_nyeri,
                soaprj_t.is_resikojatuh,
                soaprj_t.soaprj_id,
                soaprj_t.pasien_id,
                soaprj_t.catatan_dokter
               FROM pendaftaran_t
                 JOIN soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
                 JOIN ruangan_m ON soaprj_t.ruangan_id = ruangan_m.ruangan_id
                 JOIN pegawai_m ON soaprj_t.pegawai_id = pegawai_m.pegawai_id
                 JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id;
        ');
        $this->execute('
            ALTER TABLE cpptrj_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190326_071744_edit_cpptrj_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190326_071744_edit_cpptrj_v cannot be reverted.\n";

        return false;
    }
    */
}
