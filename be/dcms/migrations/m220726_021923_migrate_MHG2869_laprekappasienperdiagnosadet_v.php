<?php

use yii\db\Migration;

/**
 * Class m220726_021923_migrate_MHG2869_laprekappasienperdiagnosadet_v
 */
class m220726_021923_migrate_MHG2869_laprekappasienperdiagnosadet_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laprekappasienperdiagnosadet_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laprekappasienperdiagnosadet_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            koreksidiagnosa_t.dokterdpjp_id,
            dokterdpjp.nama_pegawai AS dokterdpjp_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            koreksidiagnosa_t.koreksidiagnosa_id,
            diagnosa_m.diagnosa_id AS diagnosa_utama_id,
            diagnosa_m.diagnosa_kode AS diagnosa_utama_kode,
            concat(diagnosa_m.diagnosa_kode, ' - ', diagnosa_m.diagnosa_nama) AS diagnosa_utama,
            concat(d_sekunder1.diag_sekunder1_kode, ' - ', d_sekunder1.diag_sekunder1_nama) AS diagnosa_sekunder1,
            concat(d_sekunder2.diag_sekunder2_kode, ' - ', d_sekunder2.diag_sekunder2_nama) AS diagnosa_sekunder2,
            concat(d_sekunder3.diag_sekunder3_kode, ' - ', d_sekunder3.diag_sekunder3_nama) AS diagnosa_sekunder3
            FROM ((((((((((koreksidiagnosa_t
            JOIN ( SELECT a.diagnosa_id,
            a.diagnosa_kode,
            a.diagnosa_nama
            FROM diagnosa_m a) diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.pasien_id,
            a.no_pendaftaran,
            a.tgl_pendaftaran,
            a.ruangan_id,
            a.is_deleted
            FROM pendaftaran_t a) pendaftaran_t ON ((koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pasienadmisi_id
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dokterdpjp ON ((koreksidiagnosa_t.dokterdpjp_id = dokterdpjp.pegawai_id)))
            LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            diagnosa_m_1.diagnosa_id AS diag_sekunder1_id,
            diagnosa_m_1.diagnosa_kode AS diag_sekunder1_kode,
            diagnosa_m_1.diagnosa_nama AS diag_sekunder1_nama
            FROM (koreksidiagnosa_t a
            JOIN diagnosa_m diagnosa_m_1 ON ((a.diagnosa_id = diagnosa_m_1.diagnosa_id)))
            WHERE ((a.kelompokdiagnosa_id = 3) AND (a.pasienadmisi_id IS NULL) AND (a.diag_asal_penyerta = '0 - '::text) AND (a.is_active = true) AND (a.is_deleted = false))
            ORDER BY a.diag_asal_penyerta) d_sekunder1 ON ((pendaftaran_t.pendaftaran_id = d_sekunder1.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            diagnosa_m_1.diagnosa_id AS diag_sekunder2_id,
            diagnosa_m_1.diagnosa_kode AS diag_sekunder2_kode,
            diagnosa_m_1.diagnosa_nama AS diag_sekunder2_nama
            FROM (koreksidiagnosa_t a
            JOIN diagnosa_m diagnosa_m_1 ON ((a.diagnosa_id = diagnosa_m_1.diagnosa_id)))
            WHERE ((a.kelompokdiagnosa_id = 3) AND (a.pasienadmisi_id IS NULL) AND (a.diag_asal_penyerta = '1 - -'::text) AND (a.is_active = true) AND (a.is_deleted = false))
            ORDER BY a.diag_asal_penyerta) d_sekunder2 ON ((pendaftaran_t.pendaftaran_id = d_sekunder2.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            diagnosa_m_1.diagnosa_id AS diag_sekunder3_id,
            diagnosa_m_1.diagnosa_kode AS diag_sekunder3_kode,
            diagnosa_m_1.diagnosa_nama AS diag_sekunder3_nama
            FROM (koreksidiagnosa_t a
            JOIN diagnosa_m diagnosa_m_1 ON ((a.diagnosa_id = diagnosa_m_1.diagnosa_id)))
            WHERE ((a.kelompokdiagnosa_id = 3) AND (a.pasienadmisi_id IS NULL) AND (a.diag_asal_penyerta = '2 - -'::text) AND (a.is_active = true) AND (a.is_deleted = false))
            ORDER BY a.diag_asal_penyerta) d_sekunder3 ON ((pendaftaran_t.pendaftaran_id = d_sekunder3.pendaftaran_id)))
            WHERE ((koreksidiagnosa_t.kelompokdiagnosa_id = 2) AND (koreksidiagnosa_t.is_active = true) AND (koreksidiagnosa_t.is_deleted = false))
            GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasien_m.no_rekam_medik, pasien_m.pasien_id, pasien_m.nama_pasien, koreksidiagnosa_t.dokterdpjp_id, dokterdpjp.nama_pegawai, ruangan_m.ruangan_id, ruangan_m.ruangan_nama, instalasi_m.instalasi_id, instalasi_m.instalasi_nama, koreksidiagnosa_t.koreksidiagnosa_id, koreksidiagnosa_t.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_nama, d_sekunder1.diag_sekunder1_kode, d_sekunder1.diag_sekunder1_nama, d_sekunder2.diag_sekunder2_kode, d_sekunder2.diag_sekunder2_nama, d_sekunder3.diag_sekunder3_kode, d_sekunder3.diag_sekunder3_nama
            ;");
        $this->execute('
            ALTER TABLE public.laprekappasienperdiagnosadet_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220726_021923_migrate_MHG2869_laprekappasienperdiagnosadet_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220726_021923_migrate_MHG2869_laprekappasienperdiagnosadet_v cannot be reverted.\n";

        return false;
    }
    */
}
