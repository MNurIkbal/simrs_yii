<?php

use yii\db\Migration;

/**
 * Class m210223_072110_migrate_20210223_3362_view_laporancarapulang_v
 */
class m210223_072110_migrate_20210223_3362_view_laporancarapulang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporancarapulang_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporancarapulang_v\" AS
            SELECT 'RJ'::text AS jenis,
            pasienpulang_t.tglpasienpulang AS tgl_pulang,
            pasien_m.no_rekam_medik,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            carakeluar_m.carakeluar_nama AS cara_pulang,
            kondisikeluar_m.kondisikeluar_nama AS kondisi_pulang
            FROM ((((((pendaftaran_t
            LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
            UNION ALL
            SELECT 'RI-RD'::text AS jenis,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pulang_rd.tglpasienpulang
            ELSE pulang_ri.tglpasienpulang
            END AS tgl_pulang,
            pasien_m.no_rekam_medik,
            pendaftaran_t.no_pendaftaran AS no_registrasi,
            pasien_m.nama_pasien,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_rd.ruangan_id
            ELSE ruangan_ri.ruangan_id
            END AS ruangan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_rd.ruangan_nama
            ELSE ruangan_ri.ruangan_nama
            END AS ruangan_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ins_rd.instalasi_id
            ELSE ins_ri.instalasi_id
            END AS instalasi_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ins_rd.instalasi_nama
            ELSE ins_ri.instalasi_nama
            END AS instalasi_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN keluar_rd.carakeluar_nama
            ELSE keluar_ri.carakeluar_nama
            END AS cara_pulang,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kondisi_rd.kondisikeluar_nama
            ELSE kondisi_ri.kondisikeluar_nama
            END AS kondisi_pulang
            FROM ((((((((((((pendaftaran_t
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN pasienpulang_t pulang_rd ON ((pendaftaran_t.pasienpulang_id = pulang_rd.pasienpulang_id)))
            LEFT JOIN pasienpulang_t pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN ruangan_m ruangan_rd ON ((pendaftaran_t.ruangan_id = ruangan_rd.ruangan_id)))
            LEFT JOIN ruangan_m ruangan_ri ON ((pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id)))
            LEFT JOIN instalasi_m ins_rd ON ((pendaftaran_t.instalasi_id = ins_rd.instalasi_id)))
            LEFT JOIN instalasi_m ins_ri ON ((ruangan_ri.instalasi_id = ins_ri.instalasi_id)))
            LEFT JOIN carakeluar_m keluar_rd ON ((pulang_rd.carakeluar_id = keluar_rd.carakeluar_id)))
            LEFT JOIN carakeluar_m keluar_ri ON ((pulang_ri.carakeluar_id = keluar_ri.carakeluar_id)))
            LEFT JOIN kondisikeluar_m kondisi_rd ON ((pulang_rd.kondisikeluar_id = kondisi_rd.kondisikeluar_id)))
            LEFT JOIN kondisikeluar_m kondisi_ri ON ((pulang_ri.kondisikeluar_id = kondisi_ri.kondisikeluar_id)))
            WHERE ((pendaftaran_t.instalasi_id = ANY (ARRAY[2, 3])) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
            ;");
            $this->execute('
                ALTER TABLE public.laporancarapulang_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210223_072110_migrate_20210223_3362_view_laporancarapulang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210223_072110_migrate_20210223_3362_view_laporancarapulang_v cannot be reverted.\n";

        return false;
    }
    */
}
