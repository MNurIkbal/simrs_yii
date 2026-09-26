<?php

use yii\db\Migration;

/**
 * Class m210707_145630_migrate_datadokter_v
 */
class m210707_145630_migrate_datadokter_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
           $this->execute('DROP VIEW if exists "public"."datadokter_v";');

           $this->execute("
            CREATE VIEW \"public\".\"datadokter_v\" AS  SELECT pegawai_m.pegawai_id,
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
    jabatan_m.jabatan_nama,
    pegawai_m.is_active,
    pegawai_m.spesialis_id,
    spesialis_m.spesialis_nama
   FROM pegawai_m
     LEFT JOIN pendidikan_m ON pegawai_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
     LEFT JOIN jabatan_m ON pegawai_m.jabatan_id = jabatan_m.jabatan_id
     LEFT JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
  WHERE pegawai_m.kelompokpegawai_id = 1 AND pegawai_m.is_active = true AND pegawai_m.is_deleted = false;
");
           $this->execute('ALTER TABLE "public"."datadokter_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210707_145630_migrate_datadokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210707_145630_migrate_datadokter_v cannot be reverted.\n";

        return false;
    }
    */
}
