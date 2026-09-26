<?php

use yii\db\Migration;

/**
 * Class m230117_104742_migrate_ga102_pegawai_v
 */
class m230117_104742_migrate_ga102_pegawai_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("drop view if exists public.pegawai_v");

        $this->execute("CREATE OR REPLACE VIEW public.pegawai_v
        AS SELECT ruangan_m.ruangan_id,
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
            concat(look_gelardepan.gelardepan, ' ', pegawai_m.nama_pegawai, ' ', gelarbelakang_m.gelarbelakang_nama) AS nama,
            ruanganpegawai_mp.is_active,
            pegawai_m.pangkat_id,
            pangkat_m.pangkat_nama,
            pegawai_m.golonganpegawai_id,
            golonganpegawai_m.golonganpegawai_nama,
            pegawai_m.gelarbelakang,
            gelarbelakang_m.gelarbelakang_nama,
            look_status_kawin.status_kawin,
            pegawai_m.agama,
            look_agama.agama AS agama_nama,
            pegawai_m.golongan_darah,
            look_golongan_darah.golongan_darah AS golongandarah_nama,
            pegawai_m.warganegara_pegawai,
            look_warganegara_pegawai.warganegara_pegawai AS warganegara_nama,
            pegawai_m.suku_id,
            suku_m.suku_nama,
            pegawai_m.warna_kulit,
            look_warna_kulit.warna_kulit AS warnakulit_nama,
            pegawai_m.propinsi_id,
            propinsi_m.propinsi_nama,
            pegawai_m.kabupaten_id,
            kabupaten_m.kabupaten_nama,
            pegawai_m.kecamatan_id,
            kecamatan_m.kecamatan_nama,
            pegawai_m.kelurahan_id,
            kelurahan_m.kelurahan_nama,
            pegawai_m.status_pegawai,
            look_status_pegawai.status_pegawai AS statuspegawai_nama,
            pegawai_m.gelardepan,
            look_gelardepan.gelardepan AS gelardepan_nama,
            pegawai_m.is_active AS pegawai_is_active,
            pegawai_m.created_date AS pegawai_created_date,
            pegawai_m.last_modified_date AS pegawai_last_modified_date,
            pegawai_m.is_online,
            pegawai_m.photopegawai AS pegawai_gambar,
            pegawai_m.photopegawai_blob,
            ruangan_m.kode_ruangan_bpjs,
            pegawai_m.kode_dokter_bpjs,
            pegawai_m.nama_dokter_bpjs
           FROM ruanganpegawai_mp
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama,
                    a.kode_ruangan_bpjs
                   FROM ruangan_m a) ruangan_m ON ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.jeniskelamin,
                    a.tempatlahir_pegawai,
                    a.tgl_lahirpegawai,
                    a.alamat_pegawai,
                    a.alamatemail,
                    a.notelp_pegawai,
                    a.nomobile_pegawai,
                    a.photopegawai,
                    a.nomorindukpegawai,
                    a.kelompokpegawai_id,
                    a.jabatan_id,
                    a.pendidikan_id,
                    a.pendkualifikasi_id,
                    a.gelarbelakang,
                    a.propinsi_id,
                    a.kabupaten_id,
                    a.kelurahan_id,
                    a.kecamatan_id,
                    a.pangkat_id,
                    a.golonganpegawai_id,
                    a.suku_id,
                    a.gelardepan,
                    a.status_kawin,
                    a.agama,
                    a.golongan_darah,
                    a.warganegara_pegawai,
                    a.warna_kulit,
                    a.status_pegawai,
                    a.is_active,
                    a.created_date,
                    a.last_modified_date,
                    a.is_online,
                    a.is_deleted,
                    a.photopegawai_blob,
                    a.kode_dokter_bpjs,
                    a.nama_dokter_bpjs
                   FROM pegawai_m a) pegawai_m ON ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id
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
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelarbelakang_m ON pegawai_m.gelarbelakang::integer = gelarbelakang_m.gelarbelakang_id
             LEFT JOIN ( SELECT a.pangkat_id,
                    a.pangkat_nama
                   FROM pangkat_m a) pangkat_m ON pegawai_m.pangkat_id = pangkat_m.pangkat_id
             LEFT JOIN ( SELECT a.golonganpegawai_id,
                    a.golonganpegawai_nama
                   FROM golonganpegawai_m a) golonganpegawai_m ON pegawai_m.golonganpegawai_id = golonganpegawai_m.golonganpegawai_id
             LEFT JOIN ( SELECT a.suku_id,
                    a.suku_nama
                   FROM suku_m a) suku_m ON pegawai_m.suku_id = suku_m.suku_id
             LEFT JOIN ( SELECT a.propinsi_id,
                    a.propinsi_nama
                   FROM propinsi_m a) propinsi_m ON pegawai_m.propinsi_id = propinsi_m.propinsi_id
             LEFT JOIN ( SELECT a.kabupaten_id,
                    a.kabupaten_nama
                   FROM kabupaten_m a) kabupaten_m ON pegawai_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT a.kecamatan_id,
                    a.kecamatan_nama
                   FROM kecamatan_m a) kecamatan_m ON pegawai_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT a.kelurahan_id,
                    a.kelurahan_nama
                   FROM kelurahan_m a) kelurahan_m ON pegawai_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS gelardepan
                   FROM lookup_m a) look_gelardepan ON pegawai_m.gelardepan::integer = look_gelardepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS status_kawin
                   FROM lookup_m a) look_status_kawin ON pegawai_m.status_kawin = look_status_kawin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS agama
                   FROM lookup_m a) look_agama ON pegawai_m.agama::integer = look_agama.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS golongan_darah
                   FROM lookup_m a) look_golongan_darah ON pegawai_m.golongan_darah = look_golongan_darah.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS warganegara_pegawai
                   FROM lookup_m a) look_warganegara_pegawai ON pegawai_m.warganegara_pegawai::integer = look_warganegara_pegawai.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS warna_kulit
                   FROM lookup_m a) look_warna_kulit ON pegawai_m.warna_kulit::integer = look_warna_kulit.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS status_pegawai
                   FROM lookup_m a) look_status_pegawai ON pegawai_m.status_pegawai::integer = look_status_pegawai.lookup_id
          WHERE pegawai_m.is_active = true AND pegawai_m.is_deleted = false AND ruanganpegawai_mp.is_deleted = false
          ORDER BY pegawai_m.nama_pegawai;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230117_104742_migrate_ga102_pegawai_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230117_104742_migrate_ga102_pegawai_v cannot be reverted.\n";

        return false;
    }
    */
}
