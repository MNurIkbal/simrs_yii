<?php

use yii\db\Migration;

/**
 * Class m220223_135253_migrate_skema_fisio_infopilihfisioterapi_v
 */
class m220223_135253_migrate_skema_fisio_infopilihfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopilihfisioterapi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopilihfisioterapi_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasien_id,
            programterapi_t.programterapi_id,
            dok_perujuk.pegawai_id AS dokter_perujuk_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            look_jenkel.lookup_kode AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
            dok_perujuk.nama_pegawai AS dokter_perujuk,
            string_agg((daftartindakan_m.daftartindakan_nama)::text, ','::text) AS daftartindakan_nama,
            programterapidetail_t.frekuensi,
            tindakanpelayanan_t.qty_tindakan AS realisasi,
            (programterapidetail_t.frekuensi - (tindakanpelayanan_t.qty_tindakan)::double precision) AS sisa,
            CASE
            WHEN (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) THEN tindakanpelayanan_t.qty_tindakan
            ELSE tindakanpelayanan_t.qty_tindakan
            END AS bayar,
            CASE
            WHEN (programterapidetail_t.frekuensi = (tindakanpelayanan_t.qty_tindakan)::double precision) THEN 'CLOSE'::text
            ELSE 'OPEN'::text
            END AS status
            FROM (((((((pendaftaran_t
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.tanggal_lahir,
            a.no_rekam_medik,
            a.jeniskelamin
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.programterapi_id
            FROM programterapi_t a) programterapi_t ON ((pendaftaran_t.pendaftaran_id = programterapi_t.pendaftaran_id)))
            LEFT JOIN ( SELECT a.programterapi_id,
            a.programterapidetail_id,
            a.dokterperujuk_id,
            a.frekuensi
            FROM programterapidetail_t a
            WHERE (a.is_deleted = false)) programterapidetail_t ON ((programterapi_t.programterapi_id = programterapidetail_t.programterapi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.daftartindakan_id,
            a.qty_tindakan,
            a.programterapidetail_id,
            a.dokterpenanggungjawab_id,
            a.tindakansudahbayar_id
            FROM tindakanpelayanan_t a
            WHERE (a.is_deleted = false)) tindakanpelayanan_t ON ((programterapidetail_t.programterapidetail_id = tindakanpelayanan_t.programterapidetail_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dok_perujuk ON ((programterapidetail_t.dokterperujuk_id = dok_perujuk.pegawai_id)))
            LEFT JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
            FROM daftartindakan_m a) daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            JOIN ( SELECT a.lookup_id,
            a.lookup_kode,
            a.lookup_name
            FROM lookup_m a) look_jenkel ON (((pasien_m.jeniskelamin)::integer = look_jenkel.lookup_id)))
            WHERE (pendaftaran_t.instalasi_id = 7)
            GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasien_id, programterapi_t.programterapi_id, dok_perujuk.pegawai_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, look_jenkel.lookup_kode, pendaftaran_t.tgl_pendaftaran, dok_perujuk.nama_pegawai, programterapidetail_t.frekuensi, tindakanpelayanan_t.qty_tindakan, tindakanpelayanan_t.tindakansudahbayar_id
            ;");
        $this->execute('
            ALTER TABLE public.infopilihfisioterapi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220223_135253_migrate_skema_fisio_infopilihfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220223_135253_migrate_skema_fisio_infopilihfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
