<?php

use yii\db\Migration;

/**
 * Class m231228_033543_migrate_MHG5434_pegawai_master_v
 */
class m231228_033543_migrate_MHG5434_pegawai_master_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."pegawai_master_v";');
        $this->execute("CREATE OR REPLACE VIEW public.pegawai_master_v
        AS SELECT pegawai_m.pegawai_id,
            pegawai_m.gelardepan,
            lkp_gelardepan.lookup_name AS gelardepan_nama,
            pegawai_m.nama_pegawai,
            pegawai_m.gelarbelakang,
            lkp_gelarbelakang.lookup_name AS gelarbelakang_nama,
            pegawai_m.status_kawin,
            lkp_statuskawin.lookup_name AS status_kawin_nama,
            pegawai_m.jeniskelamin,
            pegawai_m.tempatlahir_pegawai,
            pegawai_m.tgl_lahirpegawai,
            pegawai_m.alamat_pegawai,
            pegawai_m.agama,
            lkp_agama.lookup_name AS agama_nama,
            pegawai_m.golongan_darah,
            lkp_golongandarah.lookup_name AS golongan_darah_nama,
            pegawai_m.warganegara_pegawai AS warganegara,
            lkp_warganegara.lookup_name AS warganegara_nama,
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
            fgetnamalookup(pegawai_m.warna_kulit::integer) AS warna_kulit_nama,
            pegawai_m.status_pegawai,
            fgetnamalookup(pegawai_m.status_pegawai::integer) AS status_pegawai_nama,
            pegawai_m.bank_id,
            bank_m.nama_bank,
            bank_m.no_rekening,
            pegawai_m.created_date,
            pegawai_m.kemampuan_bahasa,
            pegawai_m.tinggibadan,
            pegawai_m.beratbadan,
            pegawai_m.npwp,
            pegawai_m.suratizinpraktek,
                CASE
                    WHEN pegawai_m.gelarbelakang::text = '9999'::text OR pegawai_m.gelarbelakang IS NULL OR pegawai_m.spesialis_id IS NULL THEN false
                    ELSE true
                END AS is_dokterumum,
            pegawai_m.tanda_tangan,
            pegawai_m.is_active,
            pegawai_satusehat_m.satusehat_pegawai_id,
            pegawai_m.noidentitas,
            pegawai_m.is_online
           FROM pegawai_m
             LEFT JOIN ( SELECT a.pendidikan_id,
                    a.pendidikan_nama
                   FROM pendidikan_m a) pendidikan_m ON pegawai_m.pendidikan_id = pendidikan_m.pendidikan_id
             LEFT JOIN ( SELECT a.pendkualifikasi_id,
                    a.pendkualifikasi_nama
                   FROM pendidikankualifikasi_m a) pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
             LEFT JOIN ( SELECT a.jabatan_id,
                    a.jabatan_nama
                   FROM jabatan_m a) jabatan_m ON pegawai_m.jabatan_id = jabatan_m.jabatan_id
             LEFT JOIN ( SELECT a.kelompokpegawai_id,
                    a.kelompokpegawai_nama,
                    a.kelompokpegawai_namalainnya,
                    a.kelompokpegawai_fungsi
                   FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
             LEFT JOIN ( SELECT a.pangkat_id,
                    a.pangkat_nama
                   FROM pangkat_m a) pangkat_m ON pegawai_m.pangkat_id = pangkat_m.pangkat_id
             LEFT JOIN ( SELECT a.suku_id,
                    a.suku_nama
                   FROM suku_m a) suku_m ON pegawai_m.suku_id = suku_m.suku_id
             LEFT JOIN ( SELECT a.bank_id,
                    a.nama_bank,
                    a.no_rekening
                   FROM bank_m a) bank_m ON pegawai_m.bank_id = bank_m.bank_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.satusehat_pegawai_id
                   FROM pegawai_satusehat_m a) pegawai_satusehat_m ON pegawai_m.pegawai_id = pegawai_satusehat_m.pegawai_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) lkp_gelardepan ON pegawai_m.gelardepan::integer = lkp_gelardepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) lkp_gelarbelakang ON pegawai_m.gelarbelakang::integer = lkp_gelarbelakang.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) lkp_statuskawin ON pegawai_m.status_kawin = lkp_statuskawin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) lkp_agama ON pegawai_m.agama::integer = lkp_agama.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) lkp_golongandarah ON pegawai_m.golongan_darah = lkp_golongandarah.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) lkp_warganegara ON pegawai_m.warganegara_pegawai::integer = lkp_warganegara.lookup_id
          WHERE pegawai_m.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231228_033543_migrate_MHG5434_pegawai_master_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231228_033543_migrate_MHG5434_pegawai_master_v cannot be reverted.\n";

        return false;
    }
    */
}
