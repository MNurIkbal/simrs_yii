<?php

use yii\db\Migration;

/**
 * Class m190903_032520_cetakjadwaldokter_v
 */
class m190903_032520_cetakjadwaldokter_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.cetakjadwaldokter_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.cetakjadwaldokter_v AS 
 SELECT jadwaldokter_m.ruangan_id,
    jadwaldokter_m.instalasi_id,
    jadwaldokter_m.pegawai_id,
    ruangan_m.ruangan_nama AS \"Poliklinik\",
    pegawai_m.nama_pegawai AS \"Dokter\",
    jadwalbukapoli_m.hari AS hari_id,
    lookup_hari.lookup_name AS \"Hari\",
    concat(jadwaldokter_m.jadwaldokter_mulai, '-', jadwaldokter_m.jadwaldokter_tutup) AS \"Waktu\",
    jadwaldokter_m.maximumantrian AS \"Kuota\",
    jadwaldokter_m.kuota_online,
    jadwaldokter_m.jadwaldokter_mulai,
    jadwaldokter_m.jadwaldokter_tutup,
    jadwaldokter_m.jadwaldokter_id,
    jadwalbukapoli_m.jam_mulai,
    jadwalbukapoli_m.jam_tutup,
    jadwalbukapoli_m.jadwalbukapoli_id,
    jadwalbukapoli_m.waktu_pelayanan
   FROM jadwaldokter_m
     JOIN ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON jadwaldokter_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
     JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id
     JOIN lookup_m lookup_hari ON lookup_hari.lookup_id = jadwalbukapoli_m.hari
  WHERE jadwaldokter_m.is_deleted = false AND jadwaldokter_m.is_active = true;");

        $this->execute('ALTER TABLE public.cetakjadwaldokter_v
                OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190903_032520_cetakjadwaldokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190903_032520_cetakjadwaldokter_v cannot be reverted.\n";

        return false;
    }
    */
}
