<?php

use yii\db\Migration;

/**
 * Class m221129_024054_migrate_MHG4226_view_dokter_v
 */
class m221129_024054_migrate_MHG4226_view_dokter_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."dokter_v";
        ');

        $this->execute('
            CREATE VIEW "public"."dokter_v" AS  SELECT ruangan_m.ruangan_id,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_nama,
    pegawai_m.pegawai_id,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    pegawai_m.jeniskelamin,
    pegawai_m.tempatlahir_pegawai,
    pegawai_m.tgl_lahirpegawai,
    pegawai_m.alamat_pegawai,
    pegawai_m.agama,
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
    ruanganpegawai_mp.is_deleted,
    instalasi_m.instalasi_nama,
    jabatan_m.jabatan_nama,
    pegawai_m.is_active,
    ruangan_m.kode_ruangan_bpjs,
    pegawai_m.kode_dokter_bpjs,
    pegawai_m.spesialis_id,
    spesialis_m.spesialis_nama
   FROM ruanganpegawai_mp 
     JOIN ruangan_m ON ruanganpegawai_mp.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pegawai_m ON ruanganpegawai_mp.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pendidikan_m ON pegawai_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN jabatan_m ON pegawai_m.jabatan_id = jabatan_m.jabatan_id
     LEFT JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
  WHERE pegawai_m.kelompokpegawai_id = 1 AND pegawai_m.is_active = true AND pegawai_m.is_deleted = false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221129_024054_migrate_MHG4226_view_dokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221129_024054_migrate_MHG4226_view_dokter_v cannot be reverted.\n";

        return false;
    }
    */
}
