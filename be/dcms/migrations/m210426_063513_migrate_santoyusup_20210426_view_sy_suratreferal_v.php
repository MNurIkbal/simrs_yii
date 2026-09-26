<?php

use yii\db\Migration;

/**
 * Class m210426_063513_migrate_santoyusup_20210426_view_sy_suratreferal_v
 */
class m210426_063513_migrate_santoyusup_20210426_view_sy_suratreferal_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.sy_suratreferal_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_suratreferal_v\" AS
            SELECT
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.tgl_pendaftaran
            ELSE pasienadmisi_t.tgl_pendaftaran
            END AS tgl_pendaftaran,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
            END AS carabayar_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pend_cabar.carabayar_nama
            ELSE adm_cabar.carabayar_nama
            END AS carabayar_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
            END AS kelaspelayanan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pend_kepel.kelaspelayanan_nama
            ELSE adm_kepel.kelaspelayanan_nama
            END AS kelaspelayanan_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
            END AS ruangan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pend_ruang.ruangan_nama
            ELSE adm_ruang.ruangan_nama
            END AS ruangan_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.instalasi_id
            ELSE adm_ins.instalasi_id
            END AS instalasi_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pend_ins.instalasi_nama
            ELSE adm_ins.instalasi_nama
            END AS instalasi_nama,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
            pasien_m.no_rekam_medik,
            pasien_m.alamat_pasien,
            pasien_m.tanggal_lahir,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS pasienjeniskelamin,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            fgetnamalookup((keluargapasien_t.keluarga_namadepan)::integer) AS keluarga_namadepan,
            keluargapasien_t.keluarga_nama,
            fgetnamalookup((keluargapasien_t.keluarga_hubungan)::integer) AS keluarga_hubungan,
            keluargapasien_t.keluarga_alamat
            FROM ((((((((((((pendaftaran_t
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN carabayar_m pend_cabar ON ((pendaftaran_t.carabayar_id = pend_cabar.carabayar_id)))
            LEFT JOIN carabayar_m adm_cabar ON ((pasienadmisi_t.carabayar_id = adm_cabar.carabayar_id)))
            LEFT JOIN kelaspelayanan_m pend_kepel ON ((pendaftaran_t.kelaspelayanan_id = pend_kepel.kelaspelayanan_id)))
            LEFT JOIN kelaspelayanan_m adm_kepel ON ((pasienadmisi_t.kelaspelayanan_id = adm_kepel.kelaspelayanan_id)))
            LEFT JOIN ruangan_m pend_ruang ON ((pendaftaran_t.ruangan_id = pend_ruang.ruangan_id)))
            LEFT JOIN ruangan_m adm_ruang ON ((pasienadmisi_t.ruangan_id = adm_ruang.ruangan_id)))
            LEFT JOIN instalasi_m pend_ins ON ((pendaftaran_t.instalasi_id = pend_ins.instalasi_id)))
            LEFT JOIN instalasi_m adm_ins ON ((adm_ruang.instalasi_id = adm_ins.instalasi_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN keluargapasien_t ON ((pasien_m.pasien_id = keluargapasien_t.pasien_id)))
            ;");
            $this->execute('
                ALTER TABLE public.sy_suratreferal_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210426_063513_migrate_santoyusup_20210426_view_sy_suratreferal_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210426_063513_migrate_santoyusup_20210426_view_sy_suratreferal_v cannot be reverted.\n";

        return false;
    }
    */
}
