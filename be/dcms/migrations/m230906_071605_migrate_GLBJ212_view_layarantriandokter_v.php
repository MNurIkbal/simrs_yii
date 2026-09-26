<?php

use yii\db\Migration;

/**
 * Class m230906_071605_migrate_GLBJ212_view_layarantriandokter_v
 */
class m230906_071605_migrate_GLBJ212_view_layarantriandokter_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."layarantriandokter_v";
        '); 

        $this->execute('
            CREATE VIEW "public"."layarantriandokter_v" AS  SELECT layarantriandetail_m.layarantriandetail_id,
    layarantriandetail_m.layarantrian_id,
    layarantriandetail_m.ruangan_id,
    layarantriandetail_m.pegawai_id,
    ruangan_m.ruangan_nama,
    pegawai_m.nama_pegawai AS pegawai_nama,
    jadwaldokter_m.jadwaldokter_id,
    concat(jadwaldokter_m.jadwaldokter_mulai, \' - \', jadwaldokter_m.jadwaldokter_tutup) AS jadwal,
    look_hari.lookup_id AS hari_id,
    look_hari.lookup_name AS hari_nama
   FROM layarantriandetail_m
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama 
           FROM ruangan_m a) ruangan_m ON ruangan_m.ruangan_id = layarantriandetail_m.ruangan_id
     JOIN ( SELECT a.ruangan_id,
            a.is_active,
            a.is_deleted
           FROM ruanganpegawai_mp a) ruanganpegawai_mp ON ruangan_m.ruangan_id = ruanganpegawai_mp.ruangan_id AND ruanganpegawai_mp.is_active = true AND ruanganpegawai_mp.is_deleted = false
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.is_active,
            a.is_deleted
           FROM pegawai_m a) pegawai_m ON layarantriandetail_m.pegawai_id = pegawai_m.pegawai_id AND pegawai_m.is_active = true AND pegawai_m.is_deleted = false
     LEFT JOIN ( SELECT a.jadwaldokter_id,
            a.jadwalbukapoli_id,
            a.ruangan_id,
            a.pegawai_id,
            a.jadwaldokter_mulai,
            a.jadwaldokter_tutup,
            a.is_bersedia
           FROM jadwaldokter_m a
          WHERE a.is_deleted IS FALSE AND a.is_active IS TRUE) jadwaldokter_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id AND jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id AND jadwaldokter_m.is_bersedia = false
     LEFT JOIN ( SELECT a.jadwalbukapoli_id,
            a.hari
           FROM jadwalbukapoli_m a) jadwalbukapoli_m ON jadwalbukapoli_m.jadwalbukapoli_id = jadwaldokter_m.jadwalbukapoli_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_hari ON jadwalbukapoli_m.hari = look_hari.lookup_id
  WHERE layarantriandetail_m.is_deleted = false AND layarantriandetail_m.is_active = true
  GROUP BY jadwaldokter_m.jadwaldokter_id, ruangan_m.ruangan_nama, pegawai_m.nama_pegawai, layarantriandetail_m.layarantriandetail_id, layarantriandetail_m.layarantrian_id, layarantriandetail_m.ruangan_id, layarantriandetail_m.pegawai_id, look_hari.lookup_id, look_hari.lookup_name, jadwaldokter_m.jadwaldokter_mulai, jadwaldokter_m.jadwaldokter_tutup;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230906_071605_migrate_GLBJ212_view_layarantriandokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230906_071605_migrate_GLBJ212_view_layarantriandokter_v cannot be reverted.\n";

        return false;
    }
    */
}
