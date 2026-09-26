<?php

use yii\db\Migration;

/**
 * Class m220101_055339_migrate_US2579_pegawai_v
 */
class m220101_055339_migrate_US2579_pegawai_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.pegawai_v;');
        $this->execute("
            CREATE VIEW \"public\".\"pegawai_v\" AS
            SELECT ruangan_m.ruangan_id,
            ruangan_m.instalasi_id,
            ruangan_m.ruangan_nama,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            pegawai_m.jeniskelamin,
            pegawai_m.tempatlahir_pegawai,
            pegawai_m.tgl_lahirpegawai,
            pegawai_m.alamat_pegawai,
            pegawai_m.alamatemail,
            pegawai_m.notelp_pegawai,
            pegawai_m.nomobile_pegawai,
            pegawai_m.photopegawai,
            pendidikan_m.pendidikan_id,
            pendidikan_m.pendidikan_nama,
            pendidikankualifikasi_m.pendkualifikasi_id,
            pendidikankualifikasi_m.pendkualifikasi_nama,
            pegawai_m.nomorindukpegawai,
            pegawai_m.kelompokpegawai_id,
            pegawai_m.jabatan_id,
            jabatan_m.jabatan_nama,
            ruanganpegawai_mp.is_deleted,
            instalasi_m.instalasi_nama,
            kelompokpegawai_m.kelompokpegawai_nama,
            kelompokpegawai_m.kelompokpegawai_namalainnya,
            kelompokpegawai_m.kelompokpegawai_fungsi,
            concat(fgetnamalookup((pegawai_m.gelardepan)::integer), ' ', pegawai_m.nama_pegawai, ' ', gelarbelakang_m.gelarbelakang_nama) AS nama,
            ruanganpegawai_mp.is_active,
            pegawai_m.pangkat_id,
            pangkat_m.pangkat_nama,
            pegawai_m.golonganpegawai_id,
            golonganpegawai_m.golonganpegawai_nama,
            pegawai_m.gelarbelakang,
            gelarbelakang_m.gelarbelakang_nama,
            fgetnamalookup(pegawai_m.status_kawin) AS status_kawin,
            pegawai_m.agama,
            fgetnamalookup((pegawai_m.agama)::integer) AS agama_nama,
            pegawai_m.golongan_darah,
            fgetnamalookup(pegawai_m.golongan_darah) AS golongandarah_nama,
            pegawai_m.warganegara_pegawai,
            fgetnamalookup((pegawai_m.warganegara_pegawai)::integer) AS warganegara_nama,
            pegawai_m.suku_id,
            suku_m.suku_nama,
            pegawai_m.warna_kulit,
            fgetnamalookup((pegawai_m.warna_kulit)::integer) AS warnakulit_nama,
            pegawai_m.propinsi_id,
            fgetnamaarea(pegawai_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
            pegawai_m.kabupaten_id,
            fgetnamaarea(NULL::integer, pegawai_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
            pegawai_m.kecamatan_id,
            fgetnamaarea(NULL::integer, NULL::integer, pegawai_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
            pegawai_m.kelurahan_id,
            fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pegawai_m.kelurahan_id) AS kelurahan_nama,
            pegawai_m.status_pegawai,
            fgetnamalookup((pegawai_m.status_pegawai)::integer) AS statuspegawai_nama,
            pegawai_m.gelardepan,
            fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan_nama,
            pegawai_m.is_active AS pegawai_is_active,
            pegawai_m.created_date AS pegawai_created_date,
            pegawai_m.last_modified_date AS pegawai_last_modified_date
            FROM (((((((((((ruanganpegawai_mp
            JOIN ruangan_m ON ((ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pegawai_m ON ((ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN pendidikan_m ON ((pegawai_m.pendidikan_id = pendidikan_m.pendidikan_id)))
            LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
            LEFT JOIN jabatan_m ON ((pegawai_m.jabatan_id = jabatan_m.jabatan_id)))
            LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
            LEFT JOIN gelarbelakang_m ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
            LEFT JOIN pangkat_m ON ((pegawai_m.pangkat_id = pangkat_m.pangkat_id)))
            LEFT JOIN golonganpegawai_m ON ((pegawai_m.golonganpegawai_id = golonganpegawai_m.golonganpegawai_id)))
            LEFT JOIN suku_m ON ((pegawai_m.suku_id = suku_m.suku_id)))
            WHERE ((pegawai_m.is_active = true) AND (pegawai_m.is_deleted = false) AND (ruanganpegawai_mp.is_active = true) AND (ruanganpegawai_mp.is_deleted = false))
            ;");
        $this->execute('
            ALTER TABLE public.pegawai_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220101_055339_migrate_US2579_pegawai_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220101_055339_migrate_US2579_pegawai_v cannot be reverted.\n";

        return false;
    }
    */
}
