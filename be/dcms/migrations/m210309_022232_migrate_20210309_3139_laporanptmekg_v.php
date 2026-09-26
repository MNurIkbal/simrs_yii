<?php

use yii\db\Migration;

/**
 * Class m210309_022232_migrate_20210309_3139_laporanptmekg_v
 */
class m210309_022232_migrate_20210309_3139_laporanptmekg_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanptmekg_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanptmekg_v\" AS
            SELECT 'RD-RJ'::text AS text,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran AS tgl_registrasi,
            pendaftaran_t.instalasi_id,
            d_utama.diagnosa_id,
            tindakanpelayanan_t.tindakanpelayanan_id,
            daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            CASE
            WHEN (((daftartindakan_m.daftartindakan_nama)::text ~~* '%ekg%'::text) OR ((daftartindakan_m.daftartindakan_nama)::text ~~* '%ecg%'::text)) THEN 'YA'::text
            ELSE 'TIDAK'::text
            END AS pemeriksaan_ekg
            FROM (((pendaftaran_t
            LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
            LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_namalainnya AS diag_utama
            FROM (koreksidiagnosa_t
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 2)) d_utama ON ((pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id)))
            WHERE ((pendaftaran_t.pasienbatalperiksa_id IS NULL) AND (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND ((pendaftaran_t.status_periksa)::integer = ANY (ARRAY[1, 2, 4, 430, 433, 486, 540, 3, 339])) AND (tindakanpelayanan_t.is_active = true) AND (tindakanpelayanan_t.is_deleted = false))
            UNION ALL
            SELECT 'RI'::text AS text,
            pendaftaran_t.pendaftaran_id,
            pasienadmisi_t.tgl_pendaftaran AS tgl_registrasi,
            ruangan_m.instalasi_id,
            d_utama.diagnosa_id,
            tindakanpelayanan_t.tindakanpelayanan_id,
            daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            CASE
            WHEN (((daftartindakan_m.daftartindakan_nama)::text ~~* '%ekg%'::text) OR ((daftartindakan_m.daftartindakan_nama)::text ~~* '%ecg%'::text)) THEN 'YA'::text
            ELSE 'TIDAK'::text
            END AS pemeriksaan_ekg
            FROM (((((pendaftaran_t
            JOIN pasienadmisi_t ON ((pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id)))
            LEFT JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN tindakanpelayanan_t ON ((pasienadmisi_t.pasienadmisi_id = tindakanpelayanan_t.pasienadmisi_id)))
            LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_namalainnya AS diag_utama
            FROM (koreksidiagnosa_t
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 2)) d_utama ON ((pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id)))
            WHERE ((pendaftaran_t.pasienbatalperiksa_id IS NULL) AND (ruangan_m.instalasi_id = 3) AND (pasienadmisi_t.status_ranap = ANY (ARRAY[440, 441, 487])) AND (tindakanpelayanan_t.is_active = true) AND (tindakanpelayanan_t.is_deleted = false))
            ;");
            $this->execute('
                ALTER TABLE public.laporanptmekg_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210309_022232_migrate_20210309_3139_laporanptmekg_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210309_022232_migrate_20210309_3139_laporanptmekg_v cannot be reverted.\n";

        return false;
    }
    */
}
