<?php

use yii\db\Migration;

/**
 * Class m190327_104332_cppt_v
 */
class m190327_104332_cppt_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW cppt_v;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW cppt_v AS 
             SELECT cppt_t.cppt_id,
                cppt_t.pendaftaran_id,
                cppt_t.pasienadmisi_id,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                cppt_t.ruangan_id,
                ruangan_m.ruangan_nama,
                kamarruangan_m.kamarruangan_nokamar,
                kamartempattidur_m.no_tempattidur,
                cppt_t.tgl_cppt,
                cppt_t.pegawai_id,
                pegawai_m.nama_pegawai,
                kelompokpegawai_m.kelompokpegawai_nama,
                cppt_t.subject,
                cppt_t.object,
                cppt_t.a_diag_utama,
                cppt_t.a_diag_penyerta,
                cppt_t.planning,
                cppt_t.instruksi,
                cppt_t.is_verifikasi,
                pegawai_verif.nama_pegawai AS pegawai_verifikasi,
                cppt_t.tgl_verifikasi,
                pegawai_m.nama_pegawai AS pegawai_cppt,
                pemberi_instruksi.nama_pegawai AS pegawai_instruksi,
                cppt_t.pemberi_instruksi_id,
                cppt_t.is_verifikasi_verbal,
                cppt_t.tgl_verif_verbal,
                cppt_t.pegawai_verbal_id,
                pegawai_verif2.nama_pegawai AS pegawai_verifikasi_verbal,
                pasienadmisi_t.pegawai_id AS dokteradmisi_id,
                dokteradmisi.nama_pegawai AS dokteradmisi_nama,
                cppt_t.is_instruksi_pulang,
                cppt_t.catatan_dokter,
                cppt_t.catatan_perawat
               FROM cppt_t
                 JOIN pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 LEFT JOIN pasienadmisi_t ON cppt_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                 JOIN pasien_m ON cppt_t.pasien_id = pasien_m.pasien_id
                 JOIN ruangan_m ON cppt_t.ruangan_id = ruangan_m.ruangan_id
                 LEFT JOIN kamarruangan_m ON cppt_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
                 LEFT JOIN kamartempattidur_m ON cppt_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
                 LEFT JOIN pegawai_m ON cppt_t.pegawai_id = pegawai_m.pegawai_id
                 LEFT JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
                 LEFT JOIN pegawai_m pegawai_verif ON cppt_t.pegawai_verifikasi_id = pegawai_verif.pegawai_id
                 LEFT JOIN pegawai_m pemberi_instruksi ON cppt_t.pemberi_instruksi_id = pemberi_instruksi.pegawai_id
                 LEFT JOIN pegawai_m pegawai_verif2 ON cppt_t.pegawai_verbal_id = pegawai_verif2.pegawai_id
                 LEFT JOIN pegawai_m dokteradmisi ON pasienadmisi_t.pegawai_id = dokteradmisi.pegawai_id;
        ');

        $this->execute('
            ALTER TABLE cppt_v
              OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_104332_cppt_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_104332_cppt_v cannot be reverted.\n";

        return false;
    }
    */
}
