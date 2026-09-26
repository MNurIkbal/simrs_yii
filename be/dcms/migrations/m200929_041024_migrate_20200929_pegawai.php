<?php

use yii\db\Migration;

/**
 * Class m200929_041024_migrate_20200929_pegawai
 */
class m200929_041024_migrate_20200929_pegawai extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."pegawai_master_v";');

        $this->execute("
            CREATE VIEW \"public\".\"pegawai_master_v\" AS  SELECT pegawai_m.pegawai_id,
    pegawai_m.gelardepan,
    fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan_nama,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    fgetnamalookup((pegawai_m.gelarbelakang)::integer) AS gelarbelakang_nama,
    pegawai_m.status_kawin,
    fgetnamalookup(pegawai_m.status_kawin) AS status_kawin_nama,
    pegawai_m.jeniskelamin,
    pegawai_m.tempatlahir_pegawai,
    pegawai_m.tgl_lahirpegawai,
    pegawai_m.alamat_pegawai,
    pegawai_m.agama,
    fgetnamalookup((pegawai_m.agama)::integer) AS agama_nama,
    pegawai_m.golongan_darah,
    fgetnamalookup(pegawai_m.golongan_darah) AS golongan_darah_nama,
    pegawai_m.warganegara_pegawai AS warganegara,
    fgetnamalookup((pegawai_m.warganegara_pegawai)::integer) AS warganegara_nama,
    pegawai_m.suku_id,
    suku_m.suku_nama,
    pegawai_m.propinsi_id,
    fgetnamaarea(pegawai_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pegawai_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pegawai_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pegawai_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pegawai_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pegawai_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pegawai_m.kelurahan_id) AS kelurahan_nama,
    pegawai_m.alamatemail,
    pegawai_m.notelp_pegawai,
    pegawai_m.nomobile_pegawai,
    pegawai_m.photopegawai,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    pendidikankualifikasi_m.pendkualifikasi_id,
    pendidikankualifikasi_m.pendkualifikasi_nama,
    pegawai_m.nomorindukpegawai,
    pegawai_m.pangkat_id,
    pegawai_m.kelompokpegawai_id,
    pegawai_m.jabatan_id,
    jabatan_m.jabatan_nama,
    pangkat_m.pangkat_nama,
    kelompokpegawai_m.kelompokpegawai_nama,
    kelompokpegawai_m.kelompokpegawai_namalainnya,
    kelompokpegawai_m.kelompokpegawai_fungsi,
    pegawai_m.warna_kulit,
    fgetnamalookup((pegawai_m.warna_kulit)::integer) AS warna_kulit_nama,
    pegawai_m.status_pegawai,
    fgetnamalookup((pegawai_m.status_pegawai)::integer) AS status_pegawai_nama,
    pegawai_m.bank_id,
    bank_m.nama_bank,
    bank_m.no_rekening,
    pegawai_m.created_date,
    pegawai_m.kemampuan_bahasa,
    pegawai_m.tinggibadan,
    pegawai_m.beratbadan,
    pegawai_m.npwp,
    pegawai_m.suratizinpraktek
   FROM (((((((pegawai_m
     LEFT JOIN pendidikan_m ON ((pegawai_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
     LEFT JOIN jabatan_m ON ((pegawai_m.jabatan_id = jabatan_m.jabatan_id)))
     LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
     LEFT JOIN pangkat_m ON ((pegawai_m.pangkat_id = pangkat_m.pangkat_id)))
     LEFT JOIN suku_m ON ((pegawai_m.suku_id = suku_m.suku_id)))
     LEFT JOIN bank_m ON ((pegawai_m.bank_id = bank_m.bank_id)))
  WHERE ((pegawai_m.is_active = true) AND (pegawai_m.is_deleted = false));");
        
        $this->execute('ALTER TABLE "public"."pegawai_master_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200929_041024_migrate_20200929_pegawai cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200929_041024_migrate_20200929_pegawai cannot be reverted.\n";

        return false;
    }
    */
}
