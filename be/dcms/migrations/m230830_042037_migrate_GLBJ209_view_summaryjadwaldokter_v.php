<?php

use yii\db\Migration;

/**
 * Class m230830_042037_migrate_GLBJ209_view_summaryjadwaldokter_v
 */
class m230830_042037_migrate_GLBJ209_view_summaryjadwaldokter_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS summaryjadwaldokter_v;
        '); 

        $this->execute('
            CREATE VIEW "public"."summaryjadwaldokter_v" AS  SELECT jadwaldokter_m.ruangan_id,
    jadwaldokter_m.pegawai_id,
    ruangan_m.ruangan_nama, 
    pegawai_m.nama_pegawai,
    sum(jadwaldokter_m.maximumantrian) AS kuota
   FROM jadwaldokter_m
     JOIN ( SELECT a.ruangan_id,
            a.poliklinik_id,
            a.ruangan_nama,
            a.kode_ruangan_bpjs,
            a.is_online
           FROM ruangan_m a) ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON jadwaldokter_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.pegawai_id,
            a.dokter_id,
            a.nama_pegawai,
            a.kode_dokter_bpjs,
            a.is_online,
            a.is_active,
            a.is_deleted
           FROM pegawai_m a) pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.jadwaldokter_id,
            a.kuota_penambahan
           FROM jadwaldoktertambahan_m a) jadwaldoktertambahan_m ON jadwaldokter_m.jadwaldokter_id = jadwaldoktertambahan_m.jadwaldokter_id
     JOIN ( SELECT a.jadwalbukapoli_id,
            a.is_deleted,
            a.shift_id,
            a.hari
           FROM jadwalbukapoli_m a) jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id AND jadwalbukapoli_m.is_deleted = false
     LEFT JOIN ( SELECT a.shift_id,
            a.shift1_id
           FROM shift_m a) shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
     LEFT JOIN ( SELECT a.notifikasi_id,
            a.judul_temp,
            a.notifikasi
           FROM notifikasi_m a) notifikasi_m ON jadwaldokter_m.notifikasi_id = notifikasi_m.notifikasi_id
     LEFT JOIN ( SELECT a.jadwaldokter_id,
            a.kuotadokter_id,
            a.is_online,
            a.kuota_tersedia
           FROM kuotadokter_r a) kuotadokter_r ON jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id AND kuotadokter_r.is_online
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS hari
           FROM lookup_m a) look_hari ON jadwalbukapoli_m.hari = look_hari.lookup_id
  WHERE jadwaldokter_m.is_deleted = false AND jadwaldokter_m.is_active = true AND pegawai_m.is_deleted = false AND pegawai_m.is_active = true
  GROUP BY jadwaldokter_m.ruangan_id, jadwaldokter_m.pegawai_id, ruangan_m.ruangan_nama, pegawai_m.nama_pegawai;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230830_042037_migrate_GLBJ209_view_summaryjadwaldokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230830_042037_migrate_GLBJ209_view_summaryjadwaldokter_v cannot be reverted.\n";

        return false;
    }
    */
}
