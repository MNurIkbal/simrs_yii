<?php

use yii\db\Migration;

/**
 * Class m220223_133846_migrate_skema_fisio_historypasienfisioterapi_v
 */
class m220223_133846_migrate_skema_fisio_historypasienfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.historypasienfisioterapi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"historypasienfisioterapi_v\" AS
            SELECT pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
            dok_perujuk.nama_pegawai AS dokter_perujuk,
            daftartindakan_m.daftartindakan_nama,
            programterapidetail_t.frekuensi,
            sum(tindakanpelayanan_t.qty_tindakan) AS realisasi,
            (programterapidetail_t.frekuensi - sum((tindakanpelayanan_t.qty_tindakan)::double precision)) AS sisa,
            CASE
            WHEN (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) THEN (0)::bigint
            ELSE sum(tindakanpelayanan_t.qty_tindakan)
            END AS bayar,
            CASE
            WHEN ((programterapidetail_t.frekuensi - sum((tindakanpelayanan_t.qty_tindakan)::double precision)) = (0)::double precision) THEN 'CLOSE'::text
            ELSE 'OPEN'::text
            END AS status
            FROM (((((pendaftaran_t
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.tanggal_lahir,
            a.no_rekam_medik,
            a.jeniskelamin
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.pendaftaran_id,
            a.daftartindakan_id,
            a.qty_tindakan,
            a.programterapidetail_id,
            a.dokterpenanggungjawab_id,
            a.tindakansudahbayar_id
            FROM tindakanpelayanan_t a
            WHERE (a.is_deleted = false)) tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
            JOIN ( SELECT a.programterapidetail_id,
            a.frekuensi
            FROM programterapidetail_t a
            WHERE (a.is_deleted = false)) programterapidetail_t ON ((tindakanpelayanan_t.programterapidetail_id = programterapidetail_t.programterapidetail_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dok_perujuk ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = dok_perujuk.pegawai_id)))
            LEFT JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
            FROM daftartindakan_m a) daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            WHERE (pendaftaran_t.instalasi_id = 7)
            GROUP BY pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), pendaftaran_t.tgl_pendaftaran, dok_perujuk.nama_pegawai, daftartindakan_m.daftartindakan_nama, programterapidetail_t.frekuensi, tindakanpelayanan_t.tindakansudahbayar_id
            ;");
        $this->execute('
            ALTER TABLE public.historypasienfisioterapi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220223_133846_migrate_skema_fisio_historypasienfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220223_133846_migrate_skema_fisio_historypasienfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
